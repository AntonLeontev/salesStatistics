<?php
$get = [
    'docnumber'  => 'test',
    'totalSum' => '321',
    'prepayment' => '123',
    'designer' => '',
    'manager' => 'Леонтьев Антон',
    'adress' => '',
    'freeDrive' => '',
];

$ch = curl_init('http://t91265r5.beget.tech?' . http_build_query($get));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_exec($ch);
curl_close($ch);

header('Location: http://t91265r5.beget.tech/base.php');
