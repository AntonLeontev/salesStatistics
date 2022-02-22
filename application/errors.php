<?php
$file = file_get_contents('errors.log');
echo nl2br($file);