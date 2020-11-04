# Firebase Phone Authentication (SMS) Usage Example for PHP5.3

## About
- Example of Firebase Phone Authentication (SMS) usage; this repository's purpose is to show a working proof-of-concept. 
- Firebase phone authentication usually used to send SMS with OTP to user's mobile phone, with intent to verify that the user is really the owner of specified phone number. 
- Common usage cases are 2-factor authentication, passwordless authentication (login with one-time-password), and verifying ownership of phone number.
- This repository already contain required package without the need to run ```composer install```. For usage in production (live/release), please install latest package from composer because this repository might be out-of-date and cause security vulnerability
- This repository are made with purpose of documenting how to do server-side idtoken-verification on php5.3; as there is no official method documented

## Documentation

### File explanation
- [login.php](login.php) = html page to show register form with phone authentication
- [ajaxcheckuserexist.php](ajaxcheckuserexist.php) = dummy ajax handler to handle phone number search (ex: check if phone number already registered) 
- [ajaxcheckidtoken.php](ajaxcheckidtoken.php) = ajax handler to handle server-side idtoken verification, for php5.3 (min version)
- [ajaxcheckidtoken_php7.php](ajaxcheckidtoken_php7.php) = same with ```ajaxcheckidtoken.php```, but for php7
- [loginpost.php](loginpost.php) = html page to handle POST from ```login.php```
- [publickey_system.gserviceaccount.com.json](publickey_system.gserviceaccount.com.json) = file json/text from https://www.googleapis.com/robot/v1/metadata/x509/securetoken@system.gserviceaccount.com
- [test-34771-f6041018142f.json](test-34771-f6041018142f.json) = file json/text with project credential (used by google to identify that the request to send SMS come from your application)

### Directory explanation
- [intl](intl) = intl-tel-input javascript library, source: https://github.com/jackocnr/intl-tel-input OR https://intl-tel-input.com/
- [vendor_jwt](vendor_jwt) = php vendor directory, result from executing ```composer require firebase/php-jwt```, use this directory for php5.3 (min version)
- [vendor_kreaitfirebase](vendor_kreaitfirebase) = php vendor directory, result from executing ```composer require kreait/firebase-php```, use this directory for php7

### Quickstart
- create a new firebase project in https://console.firebase.google.com/project then add a new app inside the project
- go to https://console.firebase.google.com/project/{project_slug}/settings/general/web then copy js configuration (tab "General", under "Your apps")
- go to https://console.firebase.google.com/project/{project_slug}/settings/serviceaccounts/adminsdk then click "Manage service account permissions"
- create a new key, then download the JSON file (in example: ```test-34771-f6041018142f.json```)
- create a html file (in example: [login.php](login.php)), in the page: 
    - load jquery js, 
    - load intl-tel-input js and css, 
    - load google APIs platform js, 
    - load core firebase SDK and firebase authentication js
    - paste js configuration from previous step
    - write register form (in example it's divided in 3 step: input phone number, input verification code, then input user information before submit)
    - write js to handle button click: after phone number input button clicked, POST to ```ajaxcheckuserexist.php```, if result body ```1``` then continue (phone number can be used to register)
    - write js to handle button click: after verification code input button clicked, POST to ```ajaxcheckidtoken.php```, if result body ```1``` then continue (verification code / OTP successfully verified)
        - important: you want to send idtoken, uid, and phoneNumber in this step
        - uid is an unique identifier string of your user
        - phoneNumber is the phone number of your user
        - idtoken is a JWT token issued by Google AFTER the user inputted a correct phone verification code; only Google can issue this token because it's digitally signed 
- create a php file (in example: [ajaxcheckuserexist.php](ajaxcheckuserexist.php)), in the file:
    - write a check if that phone number is allowed to register/login (basically if user already exist, he/she cannot register; OR if user don't exist, he/she cannot login)
    - output "1" on allowed to, or any string with error message otherwise
- create a php file (in example: [ajaxcheckidtoken.php](ajaxcheckidtoken.php)), in the file:
    - write a check if idtoken is correctly signed, not expired, and the phone number and uid of the user matches the idtoken (please read about JWT)
    - for php5.3, you will want firebase/php-jwt library, it will be faster, but it can only verify idtoken
    - for php7, you will want kreait/firebase-php (not supported for php5), it will be slower, but it also support other firebase feature (not only SMS/phone authentication)
- create a php file to handle POST from previous html file (in example: [loginpost.php](loginpost.php)), in the file:
    - write another check for idtoken validity (recommended) because malicious user can send arbitary/crafted HTTP request with simple tools (ex: postman/curl/HTTPie)
    - write your business logic for logging user in (or registering user)

## References
- https://firebase.google.com/docs/auth/admin/verify-id-tokens
- https://github.com/jackocnr/intl-tel-input
- https://intl-tel-input.com/
- https://github.com/firebase/php-jwt
- https://github.com/kreait/firebase-php
- https://firebase-php.readthedocs.io/
- https://jwt.io/
- https://www.json.org/
