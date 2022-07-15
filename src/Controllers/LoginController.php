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
        $scope = [
            'contacts',
            'timeline',
            'forms'
        ];
        $scope_string = "";
        for( $i=0; $i<count($scope); $i++ )
        {
            $scope_string .= $scope[$i];
            if( $i != count($scope) - 1 )
            {
                $scope_string .= "%20";
            }
        }
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

}
