<?php

namespace Jordanbeattie\Hubspot\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Jordanbeattie\Hubspot\Models\HubspotConnection;

trait HasHubspot {

    public function connectToHubspot($access_token, $refresh_token, $expires_in)
    {
        $this->hs_access_token = $access_token;
        $this->hs_refresh_token = $refresh_token;
        $this->hs_access_expires = Carbon::now()->addSeconds($expires_in);
        $this->save();
    }

    public function disconnectFromHubspot()
    {
        $this->hs_access_token = null;
        $this->hs_refresh_token = null;
        $this->hs_access_expires = null;
        if( $this->hubspot_sms_field )
        {
            $this->hubspot_sms_field = 0;
        }
        $this->save();
    }

    public function refreshHubspotAccess()
    {
        if( Carbon::now()->gte(Carbon::parse($this->hs_access_expires)) )
        {
            $refresh = Http::asForm()->post('https://api.hubapi.com/oauth/v1/token', [
                'grant_type'    => 'refresh_token',
                'refresh_token' => $this->hs_refresh_token,
                'client_id'     => env('HUBSPOT_CLIENT_ID'),
                'client_secret' => env('HUBSPOT_CLIENT_SECRET'),
                'redirect_uri'  => env('HUBSPOT_CALLBACK_URL')
            ]);
            if( $refresh->successful() )
            {
                $refresh_content = json_decode($refresh->body());
                $this->connectToHubspot($refresh_content->access_token, $refresh_content->refresh_token, $refresh_content->expires_in);
            }
        }
    }

    public function isConnectedToHubspot()
    {
        return ( $this->hs_access_token ? true : false );
    }

    public function hubspot()
    {
        return new HubspotConnection($this);
    }

}
