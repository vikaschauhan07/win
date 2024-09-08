<?php

namespace App\Helpers;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use App\Config;

class KlaviyoHelper
{
    public static function sendSignupEvent($data)
    {
        $client = new Client();
        
        $body = json_encode([
            "data" => [
                "type" => "event",
                "attributes" => [
                    "properties" => new \stdClass(),
                    "metric" => [
                        "data" => [
                            "type" => "metric",
                            "attributes" => [
                                "name" => "Create account"
                            ]
                        ]
                    ],
                    "profile" => [
                        "data" => [
                            "type" => "profile",
                            "attributes" => [
                                "first_name" => $data['firstname'],
                                "last_name" => $data['lastname'],
                                "properties" => [
                                    "signup_method" => "regular",
                                    "firstname" => $data['firstname'],
                                    "lastname" => $data['lastname']
                                ],
                                "email" => $data['email']
                            ]
                        ]
                    ]
                ]
            ]
        ]);

        try {
            $response = $client->request('POST', 'https://a.klaviyo.com/api/events/', [
                'body' => $body,
                'headers' => [
                    'Authorization' => 'Klaviyo-API-Key ' . Config::KLAVIYO_PRIVATE_KEY,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Revision' => '2024-06-15',
                ],
            ]);
            
            return json_decode($response->getBody(), true);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            Log::error("Client Error: " . $e->getResponse()->getBody()->getContents());
            return ['success' => false, 'message' => 'Client Error'];
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            Log::error("Server Error: " . $e->getResponse()->getBody()->getContents());
            return ['success' => false, 'message' => 'Server Error'];
        } catch (\Exception $e) {
            Log::error("General Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'An error occurred'];
        }
    }
}
