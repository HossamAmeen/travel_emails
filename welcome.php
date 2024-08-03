<?php
require __DIR__ . '/vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

// Create a Twig environment
$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader, [
    'cache' => __DIR__ . '/cache', // Optional cache directory
]);

// Data to pass to the template
$name = 'Visitor'; // Replace with actual user name if available
$time = date('H:i');

// Render the template
echo $twig->render('welcome.html.twig', ['name' => $name, 'time' => $time]);
