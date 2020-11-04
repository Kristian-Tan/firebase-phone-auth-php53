# Contoh Penggunaan Firebase Phone Authentication (SMS) untuk PHP5.3

## About
- Contoh Penggunaan Firebase Phone Authentication (SMS) digunakan untuk demo dan proof-of-concept cara menggunakan firebase phone authentication. 
- Firebase phone authentication umumnya digunakan untuk mengirim SMS berisi OTP ke ponsel pengguna aplikasi, untuk memverifikasi bahwa pengguna benar-benar pemilik nomor telepon tersebut. 
- Usage case yang umum digunakan adalah 2-factor authentication, passwordless authentication (login with one-time-password), serta memverifikasi kepemilikan nomor telepon pengguna.
- Repository ini sudah berisi package yang dibutuhkan tanpa perlu melakukan ```composer install``` untuk keperluan demo. Untuk penggunaan di server production (live/release), mohon install sendiri package yang dibutuhkan karena package bawaan repository ini bisa out-of-date dan menyebabkan security vulnerability
- Repository ini dibuat dengan alasan bahwa server-side verification terhadap idtoken tidak terdokumentasi (tidak ada cara official) untuk php5.3

## Documentation

### Penjelasan file
- [login.php](login.php) = html page untuk menampilkan form register dengan phone authentication
- [ajaxcheckuserexist.php](ajaxcheckuserexist.php) = dummy ajax handler untuk menangani pencarian nomor telepon (misal jika no.telp sudah ada tidak boleh register) 
- [ajaxcheckidtoken.php](ajaxcheckidtoken.php) = ajax handler untuk menangani server-side verification terhadap idtoken, untuk php5.3 (min version)
- [ajaxcheckidtoken_php7.php](ajaxcheckidtoken_php7.php) = sama seperti ```ajaxcheckidtoken.php```, tetapi ini untuk php7
- [loginpost.php](loginpost.php) = html page yang menangani POST dari ```login.php```
- [publickey_system.gserviceaccount.com.json](publickey_system.gserviceaccount.com.json) = file json/text hasil dari https://www.googleapis.com/robot/v1/metadata/x509/securetoken@system.gserviceaccount.com
- [test-34771-f6041018142f.json](test-34771-f6041018142f.json) = file json/text berisi credential project (digunakan google untuk mengidentifikasi bahwa permintaan kirim SMS datang dari project/app anda)

### Penjelasan directory
- [intl](intl) = intl-tel-input javascript library, sumber: https://github.com/jackocnr/intl-tel-input atau https://intl-tel-input.com/
- [vendor_jwt](vendor_jwt) = php vendor directory hasil perintah ```composer require firebase/php-jwt```, gunakan directory ini untuk php5.3 (min version)
- [vendor_kreaitfirebase](vendor_kreaitfirebase) = php vendor directory hasil perintah ```composer require kreait/firebase-php```, gunakan directory ini untuk php7

### Quickstart
- buat baru firebase project di https://console.firebase.google.com/project kemudian buat baru app pada project tsb
- buka https://console.firebase.google.com/project/{project_slug}/settings/general/web kemudian copas js configuration (tab "General", pada "Your apps")
- buka https://console.firebase.google.com/project/{project_slug}/settings/serviceaccounts/adminsdk klik "Manage service account permissions"
- buat baru key, lalu download JSON file (example: ```test-34771-f6041018142f.json```)
- buat baru html file (example: [login.php](login.php)), isi file tsb: 
    - load jquery js, 
    - load intl-tel-input js dan css, 
    - load google APIs platform js, 
    - load core firebase SDK dan firebase authentication js
    - paste js configuration dari step sebelumnya
    - tulis register form (di contoh dipecah 3 step: input no.telp, input kode verifikasi, lalu input user information sebelum submit)
    - tulis js untuk handle button click: setelah input no.telp ditekan, POST ke ```ajaxcheckuserexist.php```, jika HTTP response body ```1``` maka lanjut (no.telp bisa digunakan untuk register)
    - tulis js untuk handle button click: setelah input kode verifikasi, POST ke ```ajaxcheckidtoken.php```, jika HTTP response body ```1``` maka lanjut (kode verifikasi / OTP benar)
        - penting: pada tahap ini, anda harus mengirim idtoken, uid, dan phoneNumber
        - uid adalah id unik milik user
        - phoneNumber adalah nomor telepon user
        - idtoken adalah JWT token yang diterbitkan (dan ditandatangani/digitally signed) oleh Google SETELAH user memasukkan kode verifikasi dengan benar; hanya Google yang bisa menerbitkan token ini karena token ini ditandatangani secara digital 
- buat baru php file (example: [ajaxcheckuserexist.php](ajaxcheckuserexist.php)), isi file tsb:
    - tulis pengecekan apakah no.telp tsb diperbolehkan register/login (sederhananya, jika user tsb sudah ada maka tidak boleh register; ATAU jika user tsb blm ada maka tidak boleh login)
    - keluarkan output "1" jika diperbolehkan, atau string yang berisi error message jika tidak
- buat baru php file (example: [ajaxcheckidtoken.php](ajaxcheckidtoken.php)), isi file tsb:
    - tulis pengecekan apakah tanda tangan digital pada idtoken valid, tidak expired, dan no.telp dan uid milik user cocok dengan yang ada di idtoken (bisa baca referensi mengenai JWT)
    - untuk php5.3, gunakan library firebase/php-jwt, lebih cepat tetapi hanya bisa untuk memverifikasi idtoken
    - untuk php7, gunakan library kreait/firebase-php (not supported for php5), lebih lambat, tetapi juga support fitur firebase lainnya (bukan hanya SMS/phone authentication)
- buat baru php file untuk menangani POST dari html file sebelumnya (example: [loginpost.php](loginpost.php)), isi file tsb:
    - tulis pengecekan lagi, apakah idtoken valid (recommended), karena malicious user bisa mengirim arbitary/crafted HTTP request dengan tool sederhana (ex: postman/curl/HTTPie)
    - tulis business logic untuk user log in (atau untuk registrasi user)

## Referensi
- https://firebase.google.com/docs/auth/admin/verify-id-tokens
- https://github.com/jackocnr/intl-tel-input
- https://intl-tel-input.com/
- https://github.com/firebase/php-jwt
- https://github.com/kreait/firebase-php
- https://firebase-php.readthedocs.io/
- https://jwt.io/
- https://www.json.org/
