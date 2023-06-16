<?php

namespace App\Http\Controllers;

use App\Models\Zoho;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ZohoController extends Controller
{
    public function index()
    {
        return view('zoho.index');
    }

    /**
     * This method will check if the user is connected to zoho or not. 
     * If already connected, then will take user to the Deal And Account Form page.
     * If not connected, then will show the button to connect to the Zoho
     */
    public function checkConnection() {
        try {

            // This will check if the user is connected to the Zoho or not.
            $isConnected = false;
            if (Zoho::count()) {
                $isConnected = true;
            }
            $scope = env('ZOHO_SCOPE');
            $clientId = env('ZOHO_CLIENT_ID');
            $clientSecret = env('ZOHO_CLIENT_SECRET');
            $redirectUri = env('ZOHO_REDIRECT_URI');

            // Authentication API URL to be called if user is not connected.
            $authURL = 'https://accounts.zoho.com/oauth/v2/auth?scope=' . $scope . '&client_id=' . $clientId . '&response_type=code&access_type=offline&prompt=consent&redirect_uri=' . $redirectUri;
            return json_encode(["code" => "SUCCESS", 'status'=>'success', 'data' => ['authUrl' => $authURL, 'isConnected' => $isConnected]]);
        } catch (\Exception $e) {
            return json_encode(["code" => "ERROR", 'status'=>'error', 'message' => $e->getMessage()]);
        }
    }

    public function callback()
    {
        // Get the code that will be returned by Zoho in callback.
        // This code will be one time use only
        if (isset($_GET['code'])) {
            $code = $_GET['code'];

            // Fetch another necessary parameters from environment variable to be passed in the api to get access token.
            $scope = env('ZOHO_SCOPE');
            $clientId = env('ZOHO_CLIENT_ID');
            $clientSecret = env('ZOHO_CLIENT_SECRET');
            $redirectUri = env('ZOHO_REDIRECT_URI');
            $accountURL = 'https://accounts.zoho.com';
            
            
            // Get the Access token and Refresh Token
            $response = Http::asForm()->post($accountURL . '/oauth/v2/token', [
                'grant_type' => 'authorization_code',
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'redirect_uri' => $redirectUri,
                'code' => $code,
            ]);
            $response = $response->json();
            $response['access_token_creation_time'] = date('Y-m-d h:i:s');
            $response['created_at'] = date('Y-m-d h:i:s');
            $response['updated_at'] = date('Y-m-d h:i:s');
    
            if (isset($response['error'])) {
                return response()->json(['response' => false, 'data' => [], 'message' => $response['error']]);
            }

            // Insert the connected user data into the database.
            Zoho::insert($response);

            return redirect()->back();
        } else {
            return redirect()->back();
        }
    }

    public function submitAccountForm(Request $request)
    {
        try {
            // Fetch data from database for the access token
            $zoho = Zoho::get()->first();
            
            // Store the accounts details to the database.
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $zoho->access_token,
                'Content-Type' => 'application/json',
            ])->post($zoho->api_domain . '/crm/v2/Accounts', [
                    'data' => [
                        [
                            'Account_Name' => $request->account_name,
                            'Website' => $request->account_website,
                            'Phone' => $request->account_phone,
                        ],
                    ],
                ]);
            $response = $response->json();
            return $response;
        } catch (\Exception $e) {
            return json_encode(["code" => "ERROR", 'status'=>'error', 'message' => $e->getMessage()]);
        }
    }

    public function createAccount()
    {
        return view('zoho.forms');
    }

    public function submitDealForm(Request $request)
    {
        try {
            // Fetch data from database for the access token
            $zoho = Zoho::get()->first();
            
            // Store the deals details to the database.
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $zoho->access_token,
                'Content-Type' => 'application/json',
            ])->post($zoho->api_domain . '/crm/v2/Deals', [
                    'data' => [
                        [
                            'Deal_Name' => $request->deal_name,
                            'Stage' => $request->deal_stage,
                            'Account_Name' => [
                                "id" => $request->account
                            ],
                        ],
                    ]
                ]);
            
            $response = $response->json();
            return $response;
        } catch (\Exception $e) {
            return json_encode(["code" => "ERROR", 'status'=>'error', 'message' => $e->getMessage()]);
        }
    }

    function getAccounts() {
        try {
            // Fetch data from database for the access token
            $zoho = Zoho::get()->first();
            
            // Fetch all the accounts from Zoho to be inserted for the deal
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $zoho->access_token,
                'Content-Type' => 'application/json',
            ])->get($zoho->api_domain . '/crm/v2/accounts');
            $response = $response->json();
            return $response;
        } catch (\Exception $e) {
            return json_encode(["code" => "ERROR", 'status'=>'error', 'message' => $e->getMessage()]);
        }
        
    }
}