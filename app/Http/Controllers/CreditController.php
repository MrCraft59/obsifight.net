<?php
namespace App\Http\Controllers;

use App\Role;
use App\ShopCreditHistory;
use App\User;
use App\ShopCreditDedipassHistory;
use App\ShopCreditHipayHistory;
use App\ShopCreditPaypalHistory;
use App\ShopCreditPaysafecardHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreditController extends Controller
{
    private $request;

    private $offers = [
      'PAYPAL' => [
          950 => 10.0,
          2300 => 25.0,
          4800 => 50.0,
          9600 => 100.0
      ]
    ];

    public function add(Request $request)
    {

    }

    public function dedipassNotification(Request $request)
    {
        $this->request = $request;
        if (!$request->has('public_key') || !$request->has('code') || !$request->has('rate'))
            abort(400);
        // Check if public key match with config
        if ($request->input('public_key') !== env('DEDIPASS_PUBLIC_KEY'))
            abort(403);

        // Check if payment is valid
        $endpoint = "http://api.dedipass.com/v1/pay/?key=" . env('DEDIPASS_PUBLIC_KEY') . "&rate={$request->input('rate')}&code={$request->input('code')}";
        $client = resolve('\GuzzleHttp\Client');
        $dedipassResult = $client->request('GET', $endpoint);
        $dedipassResult = @json_decode($dedipassResult->getBody());
        if (!$dedipassResult)
            abort(403);
        if ($dedipassResult->status !== 'success')
            abort(403);

        // Check if not already in history
        if (ShopCreditDedipassHistory::where('code', $request->input('code'))->where('rate', $request->input('rate'))->count() > 0)
            abort(403);

        $money = $dedipassResult->virtual_currency;

        // Add transaction
        $transaction = new ShopCreditDedipassHistory();
        $transaction->code = $request->input('code');
        $transaction->rate = $request->input('rate');
        $transaction->payout = $dedipassResult->payout;
        $transaction->save();

        $this->save(Auth::user(), $money, $money / 80, 'DEDIPASS', $transaction);
        return redirect('/user');
    }

    public function paypalNotification(Request $request)
    {
        $this->request = $request;
        // Check request
        $paypal = resolve('\Srmklive\PayPal\Services\ExpressCheckout');
        $request->merge(['cmd' => '_notify-validate']);
        $response = (string)$paypal->verifyIPN($request->all());
        if ($response !== 'VERIFIED')
            abort(403);

        // Find user
        $user = User::where('id', $request->input('custom'))->firstOrFail();
        if (!$user->can('shop-credit-add') && strtoupper($request->input('payment_status')) !== 'CANCELED_REVERSAL')
            abort(403);
        // Check currency
        if ($request->input('mc_currency') !== 'EUR')
            abort(403);
        // Check receiver
        if ($request->input('receiver_email') !== env('PAYPAL_EMAIL'))
            abort(403);

        // Try to find transaction
        $transaction = ShopCreditPaypalHistory::where('payment_id', $request->input('txn_id'))->first();

        // Handle types
        switch (strtoupper($request->input('payment_status'))) {
            case 'COMPLETED':
                // Already handled
                if (is_object($transaction) && $transaction->status === 'COMPLETED')
                    abort(403);
                // Find offer
                $amount = floatval($request->input('mc_gross'));
                if (!in_array($amount, $this->offers['PAYPAL']))
                    abort(404);

                // Add transaction
                $transaction = new ShopCreditPaypalHistory();
                $transaction->payment_amount = $request->input('mc_gross');
                $transaction->payment_tax = $request->input('mc_fee');
                $transaction->payment_id = $request->input('txn_id');
                $transaction->buyer_email = $request->input('payer_email');
                $transaction->payment_date = $request->input('payment_date');
                $transaction->status = 'COMPLETED';
                $transaction->save();

                $this->save(
                    $user,
                    array_search($amount, $this->offers['PAYPAL']),
                    $amount,
                    'PAYPAL',
                    $transaction
                );
                break;
            case 'REVERSED':
            case 'REFUNDED':
                if (!is_object($transaction))
                    abort(404);

                // Ban user with API
                resolve('\ApiObsifight')->get('/sanction/bans', 'POST', [
                    'reason' => 'Accès au compte restreint : litige en cours',
                    'server' => '(global)',
                    'type' => 'user',
                    'user' => array(
                        'username' => $user->username
                    )
                ]);
                // Edit rank on website
                $user->attachRole(Role::where('name', 'restricted')->first());
                $user->detachRole(Role::where('name', 'user')->first());

                // Edit transaction
                $transaction->status = strtoupper($request->input('payment_status'));
                $transaction->case_date = date('Y-m-d H:i:s');
                $transaction->save();

                break;
            case 'CANCELED_REVERSAL':
                if (!is_object($transaction))
                    abort(404);

                // Unban user with API
                $api = resolve('\ApiObsifight');
                $result = $api->get("/user/{$user->username}/sanctions?limit=3");
                if ($result->status && $result->success) {
                    $banId = $result->body['bans'][0]['id'];
                    $api->get("/sanction/bans/{$banId}", 'PUT', array(
                        'remove_reason' => 'Litige clos'
                    ));
                }
                // Edit rank on website
                $user->attachRole(Role::where('name', 'user')->first());
                $user->detachRole(Role::where('name', 'restricted')->first());

                // Edit transaction
                $transaction->status = 'CANCELED_REVERSAL';
                $transaction->save();
                break;
            default:
                abort(404);
                break;
        }
    }

    public function hipayNotification(Request $request)
    {
        $this->request = $request;
    }

    private function save($user, $money, $amount, $type, $transaction)
    {
        // Save into history
        $history = new ShopCreditHistory();
        $history->transaction_type = $type;
        $history->user_id = $user->id;
        $history->money = $money;
        $history->amount = $amount;
        $history->transaction_id = $transaction->id;
        $history->save();

        // Add history to transaction
        $transaction->history_id = $history->id;
        $transaction->save();

        // Add money to user
        $currentUser = User::find($user->id);
        $currentUser->money = ($currentUser->money + floatval($money));
        $currentUser->save();

        // Notify
        $notification = new \App\Notification();
        $notification->user_id = $user->id;
        $notification->type = 'success';
        $notification->key = 'shop.credit.add.success';
        $notification->vars = ['money' => $money];
        $notification->auto_seen = 1;
        $notification->save();
    }

}