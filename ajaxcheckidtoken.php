<?php

// ajax contoh, jika meng-echo "1" artinya idtoken tersebut valid
// ini khusus php dibawah versi 7 (karena diatas php7 bisa menggunakan library kreait/firebase)

// install dengan "composer require firebase/php-jwt"
require_once("vendor_jwt/autoload.php");

// ambil dari https://firebase.google.com/docs/auth/admin/verify-id-tokens
$googlepublickey_url = "https://www.googleapis.com/robot/v1/metadata/x509/securetoken@system.gserviceaccount.com";
$googlepublickey_url_local = "publickey_system.gserviceaccount.com.json"; // ini local copy (semacam cache/backup jika link diatas mati/down)
$googlepublickey = file_get_contents($googlepublickey_url);

// jika tidak bisa ambil dari google, maka pakai yang sudah disediakan saja (warning: bisa out of date!!)
if(empty($googlepublickey)) $googlepublickey = file_get_contents($googlepublickey_url_local); 

// json decode
$googlepublickey = json_decode($googlepublickey, true);

// use \Firebase\JWT\JWT;
try {
    // set agar jam (unix timestamp) antara google firebase dengan server ini boleh maksimal selisih berapa detik 
    // (digunakan untuk signature verification)
    \Firebase\JWT\JWT::$leeway = 600;

    $decoded = \Firebase\JWT\JWT::decode(
        $_REQUEST["idtoken"], // idtoken dalam format JWT (lihat https://jwt.io untuk penjelasan format JWT)
        $googlepublickey, // array json milik google public key
        array_keys(\Firebase\JWT\JWT::$supported_algs) // perbolehkan menggunakan semua algoritma digest/hash
    );

    if($decoded->phone_number != $_REQUEST["phone"]) { echo "FAILED: phone number mismatch"; exit(); }
    if($decoded->user_id != $_REQUEST["uid"]) { echo "FAILED: user_id mismatch"; exit(); }

    echo "1";
} catch (Exception $e) {
    echo "FAILED: JWT signature verification failed"; 
}

/*
// hasil print_r $decoded:
stdClass Object
(
    [iss] => https://securetoken.google.com/atlantean-app-182405
    [aud] => atlantean-app-182405
    [auth_time] => 1603870066
    [user_id] => wfMZz5EQY4ZY2uNLEAMh5BKkOlE2
    [sub] => wfMZz5EQY4ZY2uNLEAMh5BKkOlE2
    [iat] => 1603870066
    [exp] => 1603873666
    [phone_number] => +6285330255855
    [firebase] => stdClass Object
        (
            [identities] => stdClass Object
                (
                    [phone] => Array
                        (
                            [0] => +6285330255855
                        )
                )
            [sign_in_provider] => phone
        )
)
*/
