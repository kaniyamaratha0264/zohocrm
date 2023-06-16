<p align="center">
<img src="https://h2js.zohocdn.com/crm/images/crm_logo_white_ff943ec_.svg" width="200" alt="Zoho">
</p>

## About

-   This is project where I have used frontend as Vue 3 and Backend as Laravel 10.

-   You can connect to the zoho and access your accounts and deals from Zoho CRM.

-   If you're not connected to the Zoho, then you'll be able to see button "Connect to the zoho".

-   Id you're already connected to the zoho, then you will see the forms to insert the Account and Deals to your Zoho CRM.

-   Both the forms have necessary validations which you not allow you to insert the invalid data into your Zoho CRM.

-   When any records will be inserted to the Zoho CRM, it'll show you the success message.

-   By chance if any error returned from API, then it'll show you the error message.

-   Refresh Token machanisam is also implemented. By default the access token is valid for 3600 seconds and if you'll try to access after that time, then system will check if the token is expired and then generate the new token with the help of refresh token.

## Steps to setup

-   Clone the repository

-   Install the dependencies using command **`composer install`** and then **`npm install`**

-   Copy the .env.example and create .env file.

-   Create the mysql database and add the credentials to the .env file.

-   Run the migrations : **`php artisan migrate`**

-   Login to the [Zoho developer console](https://api-console.zoho.com/)

-   Click on the **Add Client** button and select the [Server Based Application](https://prnt.sc/6ltVGq5Q2E_s)

-   Add the below details and Click on the Create button.

    -   Client name
    -   Home page URL : When you'll run the project it will give you the URL that URL you need to paste here. It will be something like : http://127.0.0.1:8000/
    -   Authorized Redirect URIs : {Home page Url}api/zoho/callback

-   Copy the Client Secret and Client ID and add into the .env file

    -   Make sure that the ZOHO_REDIRECT_URI in .env should be same as you added Authorized Redirect URI in zoho.

-   Run the project using command **`php artisan serve`** also with port if needed **`php artisan serve --port=8001`** and **`npm run dev`** in different terminals.
