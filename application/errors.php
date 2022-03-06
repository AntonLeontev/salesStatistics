<?php
use src\DatabaseConfig;
use src\DatabaseHandler;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

include_once ('vendor/autoload.php');

$file = file_get_contents('errors.log');
$strings = preg_split('~\n~', $file);
$file = array_reverse($strings);

$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader);

echo $twig->render('errors.html.twig', ['text'=>$file]);




