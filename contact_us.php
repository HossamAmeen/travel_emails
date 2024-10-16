<?php
require 'vendor/autoload.php';
require 'send_email.php';
require 'allow_cors.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);

$today = str_replace(date('F'), strtoupper(date('F')), date('d F Y'));

$_POST['today'] = $today;
$_POST['is_email'] = False;

$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader, [
    'cache' => __DIR__ . '/cache',
]);

$template_data =  $twig->render('contact_us.html.twig', $_POST);

$template_data = "<h1>test</h1>";
$file_name = "contract_us";
$user_name = $_POST['name'];

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
$pdf_path = "uploads/" . $file_name . $user_name  . '_' . date('Ymd_His'). '.pdf';
$mpdf->Output($pdf_path, 'F'); 

$lastSlashPos = strrpos($_SERVER['REQUEST_URI'] , '/');
$baseUrl = substr($_SERVER['REQUEST_URI'], 0, $lastSlashPos + 1);
$downloadLink = $_SERVER['HTTP_HOST'] . $baseUrl . '/' . $pdf_path;


$_POST['download_link'] = $downloadLink;
$_POST['is_email'] = True;
$template_data =  $twig->render('contact_us.html.twig', $_POST);

// send_email('contract us', 'Ops@whitecloudsaviation.com', $template_data, $template_data, 'contract_us', $user_name);

?>
