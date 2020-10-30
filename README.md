# Contoh Penggunaan Firebase Phone Authentication (SMS)

## About
- Contoh Penggunaan Firebase Phone Authentication (SMS) digunakan untuk demo dan proof-of-concept cara menggunakan firebase phone authentication. 
- Firebase phone authentication umumnya digunakan untuk mengirim SMS berisi OTP ke ponsel pengguna aplikasi, untuk memverifikasi bahwa pengguna benar-benar pemilik nomor telepon tersebut. 
- Usage case yang umum digunakan adalah 2-factor authentication, passwordless authentication (login with one-time-password), serta memverifikasi kepemilikan nomor telepon pengguna.
- Repository ini sudah berisi package yang dibutuhkan tanpa perlu melakukan ```composer install``` untuk keperluan demo. Untuk penggunaan di server production (live/release), mohon install sendiri package yang dibutuhkan karena package bawaan repository ini bisa out-of-date dan menyebabkan security vulnerability
- Repository ini dibuat dengan alasan bahwa server-side verification terhadap idtoken tidak terdokumentasi (tidak ada cara official) untuk php5.3

## Documentation

### File explanation
- [login.php](login.php) = html page untuk menampilkan form register dengan phone authentication
- [ajaxcheckuserexist.php](ajaxcheckuserexist.php) = dummy ajax handler untuk menangani pencarian nomor telepon (misal jika no.telp sudah ada tidak boleh register) 
- [ajaxcheckidtoken.php](ajaxcheckidtoken.php) = ajax handler untuk menangani server-side verification terhadap idtoken, untuk php5.3 (min version)
- [ajaxcheckidtoken_php7.php](ajaxcheckidtoken_php7.php) = sama seperti ```ajaxcheckidtoken.php```, tetapi ini untuk php7
- [loginpost.php](loginpost.php) = html page yang menangani POST dari ```login.php```
- [publickey_system.gserviceaccount.com.json](publickey_system.gserviceaccount.com.json) = file json/text hasil dari https://www.googleapis.com/robot/v1/metadata/x509/securetoken@system.gserviceaccount.com
- [test-34771-f6041018142f.json](test-34771-f6041018142f.json) = file json/text berisi credential project (digunakan google untuk mengidentifikasi bahwa permintaan kirim SMS datang dari project/app anda)

### Directory explanation
- [intl](intl) = intl-tel-input javascript library, sumber: https://github.com/jackocnr/intl-tel-input atau https://intl-tel-input.com/
- [vendor_jwt](vendor_jwt) = php vendor directory hasil perintah ```composer require firebase/php-jwt```, gunakan directory ini untuk php5.3 (min version)
- [vendor_kreaitfirebase](vendor_kreaitfirebase) = php vendor directory hasil perintah ```composer require kreait/firebase-php```, gunakan directory ini untuk php7

### Quickstart
- create a new firebase project in https://console.firebase.google.com/project then add a new app inside the project
- go to https://console.firebase.google.com/project/{project_slug}/settings/general/web then copy js configuration (tab "General", under "Your apps")
- go to https://console.firebase.google.com/project/{project_slug}/settings/serviceaccounts/adminsdk then click "Manage service account permissions"
- create a new key, then download the JSON file (in example: ```test-34771-f6041018142f.json```)
- create a html file (in example: ```login.php```), in the page: 
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
- create a php file (in example: ```ajaxcheckuserexist.php```), in the file:
    - write a check if that phone number is allowed to register/login (basically if user already exist, he/she cannot register; OR if user don't exist, he/she cannot login)
    - output "1" on allowed to, or any string with error message otherwise
- create a php file (in example: ```ajaxcheckidtoken.php```), in the file:
    - write a check if idtoken is correctly signed, not expired, and the phone number and uid of the user matches the idtoken (please read about JWT)
    - for php5.3, you will want firebase/php-jwt library, it will be faster, but it can only verify idtoken
    - for php7, you will want kreait/firebase-php (not supported for php5), it will be slower, but it also support other firebase feature (not only SMS/phone authentication)
- create a php file to handle POST from previous html file (in example: ```loginpost.php```), in the file:
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

## To Do
- English translation
