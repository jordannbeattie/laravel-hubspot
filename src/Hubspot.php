<?php

namespace Jordanbeattie\Hubspot;

use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class Hubspot
{

    public static function team($token)
    {
        return Team::where('hs_access_token', $token)->first();
    }

    public function get( $token, $url, $parameters = [] )
    {
        $original_url = $url;
        /* Prepare the URL */
            $url = "https://api.hubapi.com" . trim($url);
            $loop_count = 0;
            foreach( $parameters as $key => $value )
            {
                if(is_iterable($value))
                {
                    $url .= ( $loop_count == 0 ? '?' : '&' ) . $key . "=";
                    $i = 1;
                    foreach( $value as $val )
                    {
                       $url .= $val;
                       if( $i < count($value) )
                       {
                           $url .= ",";
                       }
                       $i++;
                    }
                }
                else
                {
                    $url .= ( $loop_count == 0 ? '?' : '&' ) . $key . "=" .$value;
                }
                $loop_count++;
            }
        /* Send request */
            $team = static::team($token);
            while(is_null($team))
            {
                $team = static::team($token);
            }
            $team->refreshHubspotAccess();
            $team = Team::find($team->id);
            $response = Http::withToken($team->hs_access_token)->retry(3, 100)->get($url);
            return $response->successful() ? $response->body() : null;
    }

    public function post( $token, $url, $parameters = [] )
    {
        $url = "https://api.hubapi.com" . trim($url);
        $team = static::team($token);
        while(is_null($team))
        {
            $team = static::team($token);
        }
        $team->refreshHubspotAccess();
        $response = Http::withToken($token)->retry(3, 100)->post($url, $parameters);
        return $response->body();
    }

    public function patch($token, $url, $parameters = [] )
    {
        $url = "https://api.hubapi.com" . trim($url);
        static::team($token)->refreshHubspotAccess();
        $response = Http::withToken($token)->retry(3, 100)->patch($url, $parameters);
        return $response->body();
    }

}
