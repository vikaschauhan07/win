<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserStores;
use App\Traits\ApiResponses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShopifyController extends Controller
{
    use ApiResponses;
    public function getUserstores(){
        try{
            $user = Auth::user();
            // {"id":27058733116,"name":"IQLyfe","email":"wardarodv@gmail.com","domain":"iqlyfe.com","province":"Delaware","country":"US","address1":"Nassau, DE 19969","zip":"19969","city":"Nassau","source":null,"phone":"7537132521","latitude":38.75205649999999,"longitude":-75.18768539999999,"primary_locale":"en","address2":"","created_at":"2019-09-21T18:54:58+03:00","updated_at":"2024-05-03T12:13:12+03:00","country_code":"US","country_name":"United States","currency":"USD","customer_email":"info@trifledecides.com","timezone":"(GMT+02:00) Europe\/Kiev","iana_timezone":"Europe\/Kiev","shop_owner":"IQLyfe Store","money_format":"<span class=money>${{amount}}<\/span>","money_with_currency_format":"<span class=money>${{amount}} USD<\/span>","weight_unit":"kg","province_code":"DE","taxes_included":false,"auto_configure_tax_inclusivity":false,"tax_shipping":false,"county_taxes":true,"plan_display_name":"Basic Shopify","plan_name":"basic","has_discounts":true,"has_gift_cards":false,"myshopify_domain":"baabyy.myshopify.com","google_apps_domain":null,"google_apps_login_enabled":null,"money_in_emails_format":"${{amount}}","money_with_currency_in_emails_format":"${{amount}} USD","eligible_for_payments":false,"requires_extra_payments_agreement":false,"password_enabled":false,"has_storefront":true,"finances":true,"primary_location_id":33804714044,"checkout_api_supported":true,"multi_location_enabled":true,"setup_required":false,"pre_launch_enabled":false,"enabled_presentment_currencies":["USD"],"transactional_sms_disabled":false,"marketing_sms_consent_enabled_at_checkout":false}
            $userStores = UserStores::where("userid",$user->userid)->get();
    
            $transformedStores = $userStores->transform(function ($store) {
                $storeData = json_decode($store->storedata, true);
                $store->currency = $storeData['currency'] ?? null;
                unset($store->storedata);
                return $store;
            });
        
            return $this->success($transformedStores,200);
           
        } catch(Exception $ex){
            return $this->error("Server Error",[],500);
        }
    }

    public function addStore(Request $request)
    {
        $userId = UserContext::getInstance()->getUserId();
        $shopifyDomain = $request->input('shopify_domain');
        $clientShAccessToken = $request->input('api_key');

        // Check if the store already exists
        $check = User::getUserStoreByDomainAndUserid($userId, $shopifyDomain);

        if ($check) {
            return response()->json(['error' => 'already_exists']);
        }

        // Make API call to Shopify to validate domain and API key
        $storeData = Api::request("https://$shopifyDomain", 'get', '/admin/api/2023-07/shop.json', [], [
            "X-Shopify-Access-Token: $clientShAccessToken"
        ], true);

        // Handle API errors
        if (isset($storeData['errors'])) {
            if ($storeData['errors'] === 'Not Found') {
                return response()->json(['error' => 'invalid_domain']);
            } elseif (strpos($storeData['errors'], 'Invalid API key') !== false) {
                return response()->json(['error' => 'invalid_api_key']);
            }
        } else {
            // Get access scopes
            $response = Api::request("https://$shopifyDomain", 'get', '/admin/oauth/access_scopes.json', [], [
                "X-Shopify-Access-Token: $clientShAccessToken"
            ], true);

            if (!empty($response['access_scopes'])) {
                $scopes = array_column($response['access_scopes'], 'handle');
                $scopesNeeded = ['read_products', 'write_products', 'read_locales'];

                // Check for required scopes
                foreach ($scopesNeeded as $scope) {
                    if (!in_array($scope, $scopes)) {
                        return response()->json(['error' => 'missing', 'missing' => $scope]);
                    }
                }

                // Store shop data
                $shop = $storeData['shop'];
                $name = $shop['name'];

                // Create the store in the database
                $storeCreated = User::createUserstore($userId, $shopifyDomain, $clientShAccessToken, $name, json_encode($shop));

                if ($storeCreated) {
                    return response()->json(['success' => true]);
                } else {
                    return response()->json(['error' => 'unknown']);
                }
            } else {
                return response()->json(['error' => 'empty_scopes']);
            }
        }
    }

}
