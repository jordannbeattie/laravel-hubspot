<?php

namespace Jordanbeattie\Hubspot\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Jordanbeattie\Hubspot\Facades\Hubspot;
use App\Models\Team;

class HubspotConnection extends Model
{
    use HasFactory;

    public $token, $team;

    public function __construct(Team $team)
    {
        $this->team = $team;
        $this->token = $this->team->hs_access_token;
    }

    /**** Contacts ****/
        public function contacts( $limit = null )
        {
            $url = '/crm/v3/objects/contacts/';
            $data = [];
            if( $limit )
            {
                $data['limit'] = $limit;
                $data['property'] = 'texten_sms_opt_out';
            }
            $response = Hubspot::get($url, $data, $this->token);
            $response = json_decode($response);
            return $response->results;
        }

        public function contact( $contact_id )
        {
            $url = '/crm/v3/objects/contacts/' . $contact_id;
            $data = [
                'properties' => [
                    'texten_sms_opt_out',
                    'phone',
                    'mobilephone'
                ]
            ];
            $response = Hubspot::get($url, $data, $this->token);
            return json_decode($response);
        }

        public function contactsInList( $list_id )
        {
            $url = " /contacts/v1/lists/" . $list_id . "/contacts/all";
            $offset = null;
            $hasMore = true;
            $contacts = [];
            $data = [
                'property' => 'phone&property=texten_sms_opt_out&property=mobilephone'
            ];
            while( $hasMore )
            {
                if( $offset )
                {
                    $data['vidOffset'] = $offset;
                }
                $this->team->refreshHubspotAccess();
                $results = Hubspot::get($url, $data, $this->token);
                $json_results = json_decode($results);
                $array_results = json_decode($results, true);
                if(property_exists($json_results, 'contacts'))
                {
                    $contacts = array_merge($contacts, $json_results->contacts);
                }
                if(array_key_exists('has-more', $array_results))
                {
                    $hasMore = json_decode($results, true)['has-more'];
                    $offset = json_decode($results, true)['vid-offset'];
                }
                else
                {
                    $hasMore = false;
                }
            }
            return $contacts;
        }

        public function createEngagement(Array $contacts, string $note){
            $contact_ids = [];
            $timestamp = Carbon::now()->getPreciseTimestamp(3);
            foreach( $contacts as $contact )
            {
                if(property_exists($contact, 'vid'))
                {
                    array_push($contact_ids, $contact->vid);
                }
                elseif(property_exists($contact, 'id'))
                {
                    array_push($contact_ids, $contact->id);
                }
            }
            $engagement = [
                "engagement" => [
                    "active" => true,
                    "ownerId" => 1,
                    "type" => "NOTE",
                    "timestamp" => $timestamp
                ],
                "associations" => [
                    "contactIds" => $contact_ids,
                ],
                "metadata" => [
                    "body" => $note
                ]
            ];
            return Hubspot::post("/engagements/v1/engagements", $engagement, $this->token);
        }

        public function searchForContactByPhoneNumber($number)
        {
            $phone_number = $number;
            $phone_number_without_code = substr($phone_number, 3);
            $phone_number_with_0 = "0" . $phone_number_without_code;
            $data = [
                "filterGroups" => [
                    array("filters" => [ array(
                        "propertyName" => "phone",
                        "operator" => "IN",
                        "values" => [$phone_number, $phone_number_without_code, $phone_number_with_0]
                    )]),
                    array("filters" => [ array(
                        "propertyName" => "mobilephone",
                        "operator" => "IN",
                        "values" => [$phone_number, $phone_number_without_code, $phone_number_with_0]
                    )]),
                ],
                "properties" => [
                    'phone', 'mobilephone', 'firstname', 'lastname', 'email'
                ]
            ];
            $response = Hubspot::post("/crm/v3/objects/contacts/search", $data, $this->token);
            return json_decode($response);
        }

        public function updateContact($contact, $parameters)
        {
            $data = [
                'properties' => []
            ];
            foreach( $parameters as $property => $value )
            {
                $data['properties'][$property] = $value;
            }
            $contactUrl = "/crm/v3/objects/contacts/" . $contact->id;
            return Hubspot::patch($contactUrl, $data, $this->token);
        }

        public function search($searchTerm)
        {
            $url = "/contacts/v1/search/query";
            $response = Hubspot::get($url, ['q' => $searchTerm], $this->token);
            if(is_null($response) && !property_exists($response, 'contacts'))
            {
                return null;
            }
            $contacts = json_decode($response)->contacts;
            return count($contacts) > 0 ? $contacts : null;
        }
    /**** End Contacts ****/


    /**** Lists ****/
        public function lists( $offset = 0 )
        {
            $url = "/contacts/v1/lists";
            $more = true;
            $lists = [];
            while( $more )
            {
                $results = Hubspot::get($url, ['offset' => $offset], $this->token);
                if(array_key_exists('has-more', json_decode($results, true)))
                {
                    $more = json_decode($results, true)['has-more'];
                }
                else
                {
                    $more = false;
                }
                $results = json_decode($results);
                if(property_exists($results, 'offset'))
                {
                    $offset = $results->offset;
                }
                if(property_exists($results, 'lists'))
                {
                    $lists = array_merge($results->lists, $lists);
                }
            }
            return $lists;
        }

        public function list( $list_id )
        {
            $url = "/contacts/v1/lists/" . $list_id;
            return json_decode(Hubspot::get($url, [], $this->token));
        }
    /**** End Lists ****/

    /**** Field Configuration ****/
        public function addUnsubscribeField()
        {
            if(!$this->checkForUnsubscribeField())
            {
                $url = "/crm/v3/properties/contact";
                $fieldData = [
                    'name'          => 'texten_sms_opt_out',
                    'label'         => 'Texten opt out',
                    'type'          => 'string',
                    'fieldType'     => 'text',
                    'groupName'     => 'contactinformation',
                    'objectType'    => 'contact'
                ];
                $this->team->refreshHubspotAccess();
                $fieldResponse = Hubspot::post($url, $fieldData, $this->token);
                if( $fieldResponse->successful() )
                {
                    $this->team->update(['hubspot_sms_field' => true]);
                    return true;
                }
                return false;
            }
            else
            {
                $this->team->update(['hubspot_sms_field' => true]);
            }
        }

        public function checkForUnsubscribeField()
        {
            $url = "/crm/v3/properties/contact";
            $data = [];
            $response = json_decode(Hubspot::get($url, $data, $this->token));
            if( property_exists($response, 'results') )
            {
                foreach( $response->results as $field )
                {
                    if( $field->name == "texten_sms_opt_out" )
                    {
                        return true;
                    }
                }
                return false;
            }
        }
    /**** End Field Configuration ****/


}
