<?php

// ajax contoh, jika meng-echo "1" artinya idtoken tersebut valid
// ini khusus php diatas versi 7 (library kreait/firebase tidak mendukung php dibawah versi 7)

// install dengan "composer require kreait/firebase-php"
require_once("vendor_kreaitfirebase/autoload.php");

//use Kreait\Firebase\Factory;
$factory = (new \Kreait\Firebase\Factory)->withServiceAccount('test-34771-f6041018142f.json');

//use Kreait\Firebase\Auth;
$auth = $factory->createAuth();

//use Firebase\Auth\Token\Exception\InvalidToken;

try 
{
    // verifikasi idtoken
    $verifiedIdToken = $auth->verifyIdToken($_REQUEST["idtoken"]);
} 
catch (\InvalidArgumentException $e) 
{
    echo 'The token could not be parsed: '.$e->getMessage();
    exit();
} 
catch (\Firebase\Auth\Token\Exception\InvalidToken $e) 
{
    echo 'The token is invalid: '.$e->getMessage();
    exit();
}

$uid = $verifiedIdToken->getClaim('sub');
$user = $auth->getUser($uid);

if($uid == $_REQUEST["uid"] && !empty($user) && $user->phoneNumber == $_REQUEST["phone"]){
    echo "1";
} else {
    echo "FAILED!!";
}
