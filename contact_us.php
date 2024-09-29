<?php
require 'vendor/autoload.php';
require 'send_email.php';
require 'allow_cors.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);

// $response = [];

// $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
// $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
// $phoneNumber = filter_input(INPUT_POST, 'phoneNumber', FILTER_SANITIZE_STRING);
// $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

// if ($name && !empty($name)) {
//         $name = trim($_POST['name']);
//         $response['status'] = 'success';
//         $response['message'] = 'Name received.';
//     } else {
//          http_response_code(400); 
//         $response['status'] = 'error';
//         $response['message'] = 'Name is required and cannot be empty.';
//         echo json_encode($response);
//         return;
//     }




// $templateFile = 'contact_us.html';
// $htmlContent = file_get_contents($templateFile);
// if ($htmlContent === false) {
//     die('Failed to read email template file.');
// }

// $pdf_file = 'contact_us_pdf.html';
// $pdf_content = file_get_contents($pdf_file);
// if ($pdf_content === false) {
//     die('Failed to read email template file.');
// }

// $pdf_content = str_replace('{{name}}', $name, $pdf_content);
// $pdf_content = str_replace('{{email}}', $email, $pdf_content);
// $pdf_content = str_replace('{{phoneNumber}}', $phoneNumber, $pdf_content);
// $pdf_content = str_replace('{{message}}', $message, $pdf_content);
// $today = str_replace(date('F'), strtoupper(date('F')), date('d F Y'));
// $pdf_content = str_replace('{{today}}', $today, $pdf_content);
    

// $htmlContent = str_replace('{{today}}', $today, $htmlContent);
// $htmlContent = str_replace('{{name}}', $name, $htmlContent);
// $htmlContent = str_replace('{{email}}', $email, $htmlContent);
// $htmlContent = str_replace('{{phoneNumber}}', $phoneNumber, $htmlContent);
// $htmlContent = str_replace('{{message}}', $message, $htmlContent);

// // send_email('contract us', 'hosamameen948@gmail.com', $htmlContent, $pdf_content, 'contract us', $name);
// send_email('contract us', 'AbdooTawfeek@gmail.com', $htmlContent, $pdf_content, 'contract us', $name);

$_POST['today'] = $today;
$_POST['is_email'] = False;

$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader, [
    // 'cache' => __DIR__ . '/cache',
]);

$template_data =  $twig->render('permits_landing.html.twig', $_POST);

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

send_email('contract us', 'AbdooTawfeek@gmail.com', $template_data, $template_data, 'contract_us', $name);

?>
