<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;
use \Cache;

class StatsController extends Controller
{
  public function __construct()
  {
    Carbon::setLocale(\Config::get('app.locale'));
    Carbon::setToStringFormat('d/m/Y à H:i:s');
  }

  public function index(Request $request)
  {
    return view('stats.index');
  }

  public function user(Request $request)
  {
    return view('stats.user');
  }

  public function faction(Request $request)
  {
    $faction = \App\Faction::getFromName($request->name);
    if (!$faction)
      return abort(404);
    return view('stats.faction', ['faction' => $faction]);
  }

  public function serverCount(Request $request)
  {
    if (!Cache::has('server.count')) {
      // Ping
      require base_path('vendor/xpaw/ping/MinecraftPing.php');
      require base_path('vendor/xpaw/ping/MinecraftPingException.php');
      try {
        $query = new \MinecraftPing(env('MINECRAFT_PROXY_PING_IP'), env('MINECRAFT_PROXY_PING_PORT'), 1);
        $info = $query->query();
      } catch(\MinecraftPingException $e) {
        return response()->json(['status' => false, 'count' => 0]);
      }
      $query->close();
      $count = $info['players']['online'];

      // Store
      Cache::put('server.count', $count, 1); // 1 minute
    }

    return response()->json(['status' => true, 'count' => Cache::get('server.count')]);
  }

  public function serverMax(Request $request)
  {
    if (!Cache::has('server.max')) {
      // Ping
      $file = file_get_contents('http://players.api.obsifight.net/max');
      $content = @json_decode($file, true);
      $max = @$content['max'];
      if (!$max)
        return response()->json(['status' => false, 'count' => 0]);

      // Store
      Cache::put('server.max', $max, 15); // 15 minutes
    }

    return response()->json(['status' => true, 'count' => Cache::get('server.max')]);
  }

  public function visitsCount(Request $request)
  {
    if (!Cache::has('visits.count')) {
      // Ping
      require base_path('vendor/eywek/obsifight/Google/GoogleAnalytics.php');
      $count = (new \GoogleAnalytics)->getVisitsFromTo('2015-10-05', 'today');

      // Store
      Cache::put('visits.count', $count, 120); // 2 hours
    }

    return response()->json(['status' => true, 'count' => Cache::get('visits.count')]);
  }
}
