<?php
namespace App\Http\Controllers;

use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Illuminate\Support\Facades\Event;

use Illuminate\Support\Facades\Auth;
use App\User;
use App\UsersToken;

use App\Mail\UserSignup;
use App\Mail\UserForgotPassword;
use Illuminate\Support\Facades\Mail;

use Carbon\Carbon;

class UserController extends Controller
{
  public function __construct()
  {
    Carbon::setLocale(\Config::get('app.locale'));
    Carbon::setToStringFormat('d/m/Y à H:i:s');
  }

  public function login(Request $request)
  {
    // Check form
    if (!$request->has('username') || !$request->has('password'))
      return response()->json([
        'status' => false,
        'error' => __('form.error.fields')
      ]);

    // Check if not temporaly disabled
    $lastRetry = \App\UsersLoginRetry::where('ip', $request->ip())->where('updated_at', '>', date('Y-m-d H:i:s', strtotime('-30 minutes')))->first();
    if (!empty($lastRetry) && $lastRetry->count >= 10) // Temporaly blocked
      return response()->json([
        'status' => false,
        'error' => __('user.login.error.blocked')
      ]);

    // Check credentials
    $user = User::where('username', $request->input('username'))->first();
    if (empty($user)) // User not found
      return response()->json([
        'status' => false,
        'error' => __('user.login.error.notfound')
      ]);
    if ($user->password !== User::hash($request->input('password'), $request->input('username'))) { // invalid password
      // save try
      $count = (empty($lastRetry)) ? 1 : ($lastRetry->count + 1);
      \App\UsersLoginRetry::updateOrCreate(
        ['ip' => $request->ip()],
        ['count' => $count]
      );
      // send response
      return response()->json([
        'status' => false,
        'error' => __('user.login.error.credentials')
      ]);
    }

    // Check if two factor auth
    $twoFactorAuthSecret = \App\UsersTwoFactorAuthSecret::where('user_id', $user->id)->first();
    if (!empty($twoFactorAuthSecret) && $twoFactorAuthSecret->enabled) { // TwoFactor is enabled, store user_id & send to user
      session(['twoFactorAuth' => ['user_id' => $user->id, 'remember_me' => $request->input('remember_me')]]);
      return response()->json([
        'status' => true,
        'twoFactorAuth' => true,
        'success' => ''
      ]);
    }

    // Success
    return $this->__loginSuccess($user->id, $request->input('remember_me'), $request);
  }

  public function validLogin(Request $request)
  {
    // Check form
    if (!$request->has('code'))
      return response()->json([
        'status' => false,
        'error' => __('form.error.fields')
      ]);
    // Check session
    if (!$request->session()->has('twoFactorAuth'))
      return abort(403);
    $userId = $request->session()->get('twoFactorAuth')['user_id'];
    $rememberMe = $request->session()->get('twoFactorAuth')['remember_me'];

    // Get secret
    $twoFactorAuthSecret = \App\UsersTwoFactorAuthSecret::where('user_id', $userId)->first();
    if (empty($twoFactorAuthSecret) || !$twoFactorAuthSecret->enabled)
      return abort(403);

    // Valid secret
    require __DIR__ . '/../../../vendor/PHPGangsta/GoogleAuthenticator.php';
    $ga = new \PHPGangsta_GoogleAuthenticator();

    // check code
    $checkResult = $ga->verifyCode($twoFactorAuthSecret->secret, $request->input('code'), 2);    // 2 = 2*30sec clock tolerance
    if (!$checkResult)
      return response()->json([
        'status' => false,
        'error' => __('user.login.error.two_factor_auth')
      ]);

    // Remove session
    $request->session()->forget('twoFactorAuth');

    return $this->__loginSuccess($userId, $rememberMe, $request);
  }

  private function __loginSuccess($userId, $rememberMe, $request)
  {
    // Log user
    Auth::loginUsingId($userId, ($rememberMe ? true : false));

    return response()->json([
      'status' => true,
      'success' => __('user.login.success')
    ]);
  }

  public function logout(Request $request)
  {
    Auth::logout();
    return redirect('/');
  }

  public function signup(Request $request)
  {
    // Check form
    if (!$request->has('username') || !$request->has('password') || !$request->has('password_confirmation') || !$request->has('email'))
      return response()->json([
        'status' => false,
        'error' => __('form.error.fields')
      ]);
    if (!$request->has('legal'))
      return response()->json([
        'status' => false,
        'error' => __('user.signup.error.legal')
      ]);
    $gResponse = \ReCaptcha::parseInput($request->input('g-recaptcha-response'));
    if ((env('APP_ENV') !== 'testing' && !$gResponse->isSuccess()) || (env('APP_ENV') === 'testing' && $request->input('g-recaptcha-response') !== 'test'))
      return response()->json([
        'status' => false,
        'error' => __('user.signup.error.captcha')
      ]);

    // Check username
    if (!preg_match('`^([a-zA-Z0-9_]{2,16})$`', $request->input('username')))
      return response()->json([
        'status' => false,
        'error' => __('user.signup.error.username')
      ]);
    // Check email
    if (Validator::make(['email' => $request->input('email')], ['email' => 'required|email'])->fails())
      return response()->json([
        'status' => false,
        'error' => __('user.signup.error.email')
      ]);
    // Check passwords
    if ($request->input('password') !== $request->input('password_confirmation'))
      return response()->json([
        'status' => false,
        'error' => __('user.signup.error.passwords')
      ]);

    // Check if username or email is already used
    $findUserWithUsernameOrEmail = User::where('username', $request->input('username'))->orWhere('email', $request->input('email'))->first();
    if (!empty($findUserWithUsernameOrEmail)) {
      if ($findUserWithUsernameOrEmail->username == $request->input('username'))
        return response()->json([
          'status' => false,
          'error' => __('user.signup.error.username.taken')
        ]);
      else
        return response()->json([
          'status' => false,
          'error' => __('user.signup.error.email.taken')
        ]);
    }

    // register user
    $user = new User();
    $user->username = $request->input('username');
    $user->email = $request->input('email');
    $user->password = User::hash($request->input('password'), $request->input('username'));
    $user->ip = $request->ip();
    $user->save();
    $user->roles()->attach(1); // attach default role

    // generate confirmation token
    $token = UsersToken::generate('EMAIL', $user->id);
    $link = action('UserController@confirmEmail', ['token' => $token]);

    // send confirmation mail
    Mail::to($user->email)->send(new UserSignup($user, $link));

    // log user
    Auth::loginUsingId($user->id, false);

    // success response
    return response()->json([
      'status' => true,
      'success' => __('user.signup.success'),
      'redirect' => url('/user')
    ]);
  }

  public function confirmEmail(Request $request)
  {
    // Find token
    $token = UsersToken::where('token', $request->token)->where('type', 'EMAIL')->where('used_ip', null)->firstOrFail();

    // Set token as used
    $token->used_ip = $request->ip();
    $token->save();

    // Redirect with flash
    return redirect('/user')->with('flash.success', __('user.signup.email.confirmed'));
  }

  public function sendConfirmationMail(Request $request)
  {
    // Find token
    $token = UsersToken::where('user_id', Auth::user()->id)->where('type', 'EMAIL')->where('used_ip', null)->firstOrFail();
    $link = action('UserController@confirmEmail', ['token' => $token->token]);

    // send confirmation mail
    Mail::to(Auth::user()->email)->send(new UserSignup(Auth::user(), $link));

    // Redirect with flash
    return redirect('/user')->with('flash.success', __('user.signup.email.confirmation.sended'));
  }

  public function profile(Request $request)
  {
    // VOTES
    $votesCount = \App\Vote::where('user_id', Auth::user()->id)->where('created_at', '>', date('Y-m-00 00:00:00'))->count();
    $rewardsWaitedCount = \App\Vote::where('user_id', Auth::user()->id)->where('reward_getted', false)->count();

    // EMAIL CONFIRMED
    $findEmailToken = UsersToken::where('user_id', Auth::user()->id)->where('type', 'EMAIL')->where('used_ip', null)->first();
    $confirmedAccount = (empty($findEmailToken));

    // TWO FACTOR AUTH
    $findTwoFactorAuthSecret = \App\UsersTwoFactorAuthSecret::where('user_id', Auth::user()->id)->first();
    $twoFactorEnabled = ($findTwoFactorAuthSecret && $findTwoFactorAuthSecret->enabled);

    // OBSIGUARD
    $findObsiGuardIPs = \App\UsersObsiguardIp::where('user_id', Auth::user()->id)->get();

    // NOTIFICATION
    $notifications = \App\Notification::getUnseen(Auth::user()->id);

    // RENDER
    return view('user.profile', compact('votesCount', 'rewardsWaitedCount', 'confirmedAccount', 'twoFactorEnabled', 'findObsiGuardIPs', 'notifications'));
  }

  public function forgotPassword(Request $request)
  {
    if (!$request->has('email'))
      return response()->json([
        'status' => false,
        'error' => __('form.error.fields')
      ]);
    // find user
    $user = User::where('email', $request->input('email'))->first();
    if (!$user)
      return response()->json([
        'status' => false,
        'error' => __('user.password.forgot.user.notfound')
      ]);

    // generate reset token
    $token = UsersToken::generate('PASSWORD', $user->id);
    $link = url('/user/password/reset/' . $token);

    // send confirmation mail
    Mail::to($user->email)->send(new UserForgotPassword($user, $link));

    // success
    return response()->json([
      'status' => true,
      'success' => __('user.password.forgot.success')
    ]);
  }

  public function resetPassword(Request $request)
  {
    // Check form
    if (!$request->has('password') || !$request->has('password_confirmation'))
      return response()->json([
        'status' => false,
        'error' => __('form.error.fields')
      ]);
    if ($request->input('password') !== $request->input('password_confirmation'))
      return response()->json([
        'status' => false,
        'error' => __('user.signup.error.passwords')
      ]);

    // Find token
    $token = UsersToken::where('token', $request->token)->where('type', 'PASSWORD')->where('used_ip', null)->where('created_at', '>', date('Y-m-d H:i:s', strtotime('-24 hours')))->firstOrFail();

    // Set token as used
    $token->used_ip = $request->ip();
    $token->save();

    // edit password
    $user = User::where('id', $token->user_id)->firstOrFail();
    $user->password = User::hash($request->input('password'), $user->username);
    $user->save();

    // login
    Auth::loginUsingId($user->id, false);

    // Redirect with flash
    return response()->json([
      'status' => true,
      'success' => __('user.password.reset.success'),
      'redirect' => url('/user')
    ]);
  }

  public function editPassword(Request $request)
  {
    // Check form
    if (!$request->has('password') || !$request->has('password_confirmation'))
      return response()->json([
        'status' => false,
        'error' => __('form.error.fields')
      ]);
    if ($request->input('password') !== $request->input('password_confirmation'))
      return response()->json([
        'status' => false,
        'error' => __('user.signup.error.passwords')
      ]);

    // edit password
    $user = User::where('id', Auth::user()->id)->firstOrFail();
    $user->password = User::hash($request->input('password'), $user->username);
    $user->save();

    // success
    return response()->json([
      'status' => true,
      'success' => __('user.password.edit.success')
    ]);
  }

  public function requestEditEmail(Request $request)
  {
    // Check form
    if (!$request->has('email') || !$request->has('reason'))
      return response()->json([
        'status' => false,
        'error' => __('form.error.fields')
      ]);
    // Check email
    if (Validator::make(['email' => $request->input('email')], ['email' => 'required|email'])->fails())
      return response()->json([
        'status' => false,
        'error' => __('user.signup.error.email')
      ]);
    // Check if username or email is already used
    $findUserWithEmail = User::where('email', $request->input('email'))->first();
    if (!empty($findUserWithEmail))
      return response()->json([
        'status' => false,
        'error' => __('user.signup.error.email.taken')
      ]);

    // check if already requested
    $emailRequest = \App\UsersEmailEditRequest::where('user_id', Auth::user()->id)->first();
    if (count($emailRequest) > 0)
      return response()->json([
        'status' => false,
        'error' => __('user.email.edit.request.already')
      ]);

    // add request
    $emailRequest = new \App\UsersEmailEditRequest();
    $emailRequest->user_id = Auth::user()->id;
    $emailRequest->email = $request->input('email');
    $emailRequest->reason = $request->input('reason');
    $emailRequest->ip = $request->ip();
    $emailRequest->save();

    // success
    return response()->json([
      'status' => true,
      'success' => __('user.email.edit.request.success')
    ]);
  }

  public function editUsername(Request $request)
  {
    // Check if purchased it
    $findAbility = \App\UsersEditUsernameAbility::where('user_id', Auth::user()->id)->where('history_id', NULL)->first();
    if (count($findAbility) <= 0)
      return response()->json([
        'status' => false,
        'error' => __('user.profile.edit.username.error.purchase')
      ]);
    // Check if already edited 2 times
    $findEdits = \App\UsersEditUsernameHistory::where('user_id', Auth::user()->id)->orderBy('id', 'DESC')->get();
    if (count($findEdits) >= 2)
      return response()->json([
        'status' => false,
        'error' => __('user.profile.edit.username.error.two_times')
      ]);
    // Check if edited last 2 weeks
    if (isset($findEdits[0]) && time() < $findEdits[0]->created_at->addWeeks(3)->timestamp)
      return response()->json([
        'status' => false,
        'error' => __('user.profile.edit.username.error.two_weeks')
      ]);
    // Check form
    if (!$request->has('username') || !$request->has('password'))
      return response()->json([
        'status' => false,
        'error' => __('form.error.fields')
      ]);
    // check password
    if (User::hash($request->input('password'), Auth::user()->username) !== Auth::user()->password)
      return response()->json([
        'status' => false,
        'error' => __('user.profile.edit.username.error.password')
      ]);

    // check username
    if (!preg_match('`^([a-zA-Z0-9_]{2,16})$`', $request->input('username')))
      return response()->json([
        'status' => false,
        'error' => __('user.signup.error.username')
      ]);
    // check if used
    $findUserWithUsername = User::where('username', $request->input('username'))->first();
    if (!empty($findUserWithUsername))
      return response()->json([
        'status' => false,
        'error' => __('user.signup.error.username.taken')
      ]);

    // edit database
    $user = User::find(Auth::user()->id);
    $user->username = $request->input('username');
    $user->password = User::hash($request->input('password'), $request->input('username'));
    $user->save();

    // add log
    $log = new \App\UsersEditUsernameHistory();
    $log->user_id = Auth::user()->id;
    $log->old_username = Auth::user()->username;
    $log->new_username = $request->input('username');
    $log->ip = $request->ip();
    $log->save();

    // remove ability
    $findAbility->history_id = $log->id;
    $findAbility->save();

    // success message
    return response()->json([
      'status' => true,
      'success' => __('user.profile.edit.username.success')
    ]);
  }

  public function transferMoney(Request $request)
  {
    // Check form
    if (!$request->has('amount') || !$request->has('to'))
      return response()->json([
        'status' => false,
        'error' => __('form.error.fields')
      ]);
    if (Validator::make(['amount' => $request->input('amount')], ['amount' => 'required|numeric'])->fails())
      return response()->json([
        'status' => false,
        'error' => __('user.profile.transfer.money.error.amount')
      ]);

    // check username
    if (Auth::user()->username == $request->input('to'))
      return response()->json([
        'status' => false,
        'error' => __('user.profile.transfer.money.error.himself')
      ]);
    $recipientUser = User::where('username', $request->input('to'))->first();
    if (count($recipientUser) <= 0)
      return response()->json([
        'status' => false,
        'error' => __('user.profile.transfer.money.error.unknown_user')
      ]);

    // check amount
    if (Auth::user()->money <= 0)
      return response()->json([
        'status' => false,
        'error' => __('user.profile.transfer.money.error.no_enough')
      ]);
    $currentUser = User::find(Auth::user()->id);
    if (($currentUser->money - floatval($request->input('amount'))) < 0)
      return response()->json([
        'status' => false,
        'error' => __('user.profile.transfer.money.error.no_enough')
      ]);

    // edit users
    $currentUser->money = ($currentUser->money - floatval($request->input('amount')));
    $currentUser->save();
    $recipientUser->money = ($recipientUser->money + floatval($request->input('amount')));
    $recipientUser->save();

    // add to history
    $log = new \App\UsersTransferMoneyHistory();
    $log->user_id = $currentUser->id;
    $log->amount = $request->input('amount');
    $log->to = $recipientUser->id;
    $log->ip = $request->ip();
    $log->save();

    // success message
    return response()->json([
      'status' => true,
      'success' => __('user.profile.transfer.money.success', ['money' => $request->input('amount'), 'username' => $recipientUser->username]),
      'money' => $currentUser->money
    ]);
  }
}
