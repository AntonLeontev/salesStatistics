<?php
$get = array(
    'name'  => 'test',
    'email' => 'русский текст'
);

$ch = curl_init('http://t91265r5.beget.tech?' . http_build_query($get));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_exec($ch);
curl_close($ch);
?>
<a href="http://t91265r5.beget.tech/base.php">BASE</a>
