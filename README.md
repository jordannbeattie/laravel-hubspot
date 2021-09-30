# Laravel Hubspot
## Requirements
### Tailwind
This package relies on having TailwindCSS installed.
Copy the following codes to your tailwind.config.js file to display the correct colours: 
```
theme: {
    colors: {
        hubspot: {
            lorax: '#FF7A59',
            sorbet: '#FF8F59',
            olaf: '#FFFFFF'
        }
    }
},
```
## Oauth
This package includes Oauth implementation. To include the login button, use the view component. 
```
<x-hubspot::oauth-button />
```
You can pass in the copy to display as well as disabling the Hubspot icon. 
```
<x-hubspot::oauth-button logo="false" copy="Login now" />
```
You must also include a callback URL in your .env file. 
```
HUBSPOT_CALLBACK_URL=/hubspot/auth/login
```
and a route in your web.php file to accept the response. Errors will be handled before reaching this point and throw 500 errors. This route will recieve the following parameters: 
- refresh_token
- access_token
- expires_in
