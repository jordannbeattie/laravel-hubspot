<?php

namespace Jordanbeattie\Hubspot\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{

    public function redirectToHubspot(Request $request)
    {
        $client_id = getenv('HUBSPOT_CLIENT_ID');
        $callback_url = route('hubspot.auth.callback');
        $scope_string = static::processScopes();
        $url =  "https://app.hubspot.com/oauth/authorize?client_id=" . $client_id .
                "&redirect_uri=" . $callback_url .
                "&scope=" . $scope_string;
        if( $request->has('returnTo') )
        {
            $url .= "&state=". $request->get('returnTo');
        }
        return redirect($url);
    }

    public function hubspotCallback(Request $request)
    {
        $auth_req = Http::asForm()->post('https://api.hubapi.com/oauth/v1/token', [
            'grant_type'    => 'authorization_code',
            'client_id'     => getenv('HUBSPOT_CLIENT_ID'),
            'client_secret' => getenv('HUBSPOT_CLIENT_SECRET'),
            'redirect_uri'  => route('hubspot.auth.callback'),
            'code'          => $request->get('code')
        ]);
        if( $auth_req->successful() )
        {
            $response       = $auth_req->json();
            $refresh_token  = $response['refresh_token'];
            $access_token   = $response['access_token'];
            $expires_in     = $response['expires_in'];
            $return_url     = getenv('HUBSPOT_CALLBACK_URL') .
                                '?refresh_token=' . $refresh_token .
                                '&access_token=' . $access_token .
                                '&expires_in=' . $expires_in;
            if($request->has('state'))
            {
                $return_url .= "&state=" . $request->get('state');
            }
            return redirect($return_url);
        }
        else
        {
            abort(500, $auth_req->status());
        }
    }
    
    private static function processScopes()
    {

        // Set required scopes
        $scopes = ['oauth'];

        // Check for user-defined scopes
        $envscopes = getenv('HUBSPOT_SCOPES');

        // Merge user-defined and required scopes
        if( $envscopes != false )
        {
            $envscopes = explode(",", $envscopes);  
            foreach( $envscopes as $scope )
            {
                if(!in_array( $scope, $scopes ) && !empty($scope))
                {
                    array_push( $scopes, $scope );
                }
            } 
        }

        // Create a useable string
        return implode("%20", $scopes);
        
    }

}
