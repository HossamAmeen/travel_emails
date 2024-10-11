<?php
require 'vendor/autoload.php';
require 'send_email.php';
require 'allow_cors.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

// Create a Twig environment
$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader, [
    // 'cache' => __DIR__ . '/cache',
]);
$file_name = "aircraft_fueling";
$user_name = $_POST['operatorName'];
$today = str_replace(date('F'), strtoupper(date('F')), date('d F Y'));

$_POST['today'] = $today;
$_POST['is_email'] = False;

$template_data =  $twig->render('aircraft_fueling.html.twig', $_POST);

if (!is_dir("uploads")) {
    mkdir("uploads", 0777, true);
}

if (!is_dir("uploads/" . $file_name)) {
    mkdir("uploads/" . $file_name, 0777, true);
}

$user_name = strtolower($user_name);
if (!is_dir("uploads/" . $file_name . '/' . $user_name)) {
    mkdir("uploads/" . $file_name . '/' . $user_name, 0777, true);
}

$mpdf = new \Mpdf\Mpdf(['default_font' => 'dejavusans']);
$mpdf->WriteHTML($template_data);

$pdf_path = "uploads/" . $file_name. '/' . $user_name . '/' . '_' . date('Ymd_His'). '.pdf';
$mpdf->Output($pdf_path, 'F'); 


$lastSlashPos = strrpos($_SERVER['REQUEST_URI'] , '/');
$baseUrl = substr($_SERVER['REQUEST_URI'], 0, $lastSlashPos + 1);
$downloadLink = $_SERVER['HTTP_HOST'] . $baseUrl . '/' . $pdf_path;
$_POST['download_link'] = $downloadLink;
$_POST['is_email'] = True;

$template_data =  $twig->render('aircraft_fueling.html.twig', $_POST);

send_email('aircraft fueling', 'Charter@whitecloudsaviation.com' , $template_data, $template_data, $file_name, $user_name);


?>