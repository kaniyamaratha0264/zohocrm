<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Http;
use App\Models\Zoho;

class ZohoTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the access token is expired or not set
        if ($this->isAccessTokenExpired()) {
            // Access token is expired, so refresh it
            $refreshToken = $this->getRefreshToken();

            // Make a request to Zoho's token endpoint to refresh the access token
            $response = Http::asForm()->post('https://accounts.zoho.com/oauth/v2/token', [
                'grant_type' => 'refresh_token',
                'client_id' => env('ZOHO_CLIENT_ID'),
                'client_secret' => env('ZOHO_CLIENT_SECRET'),
                'refresh_token' => $refreshToken,
            ]);

            if ($response->successful()) {

                \Log::debug($response->json());
                $responseData = $response->json();
                $accessToken = $responseData['access_token'];

                // Update the access token in the request headers
                $request->headers->set('Authorization', 'Bearer ' . $accessToken);

                // Update the access token in the session or wherever you store it
                $this->updateAccessToken($accessToken);
            }
        }
        return $next($request);
    }

    private function isAccessTokenExpired()
    {
        
        $zoho = Zoho::get()->first();

        $createdAt = $zoho->access_token_creation_time;
        $expirationTime = 3600; // Expiration time in seconds

        // Calculate the expiration timestamp by adding the expiration time to the created_at timestamp
        $expirationTimestamp = strtotime($createdAt) + $expirationTime;

        // Compare the expiration timestamp with the current timestamp
        $currentTimestamp = time();
        // dd($expirationTimestamp, $currentTimestamp ,$expirationTimestamp <= $currentTimestamp);
        return $expirationTimestamp <= $currentTimestamp;
    }

    private function getRefreshToken()
    {
        $zoho = Zoho::get()->first();

        return $zoho->refresh_token;
    }

    private function updateAccessToken($accessToken)
    {
        $zoho = Zoho::get()->first();

        $zoho->access_token = $accessToken;
        $zoho->access_token_creation_time = date('Y-m-d h:i:s');

        $zoho->save();
    }
}
