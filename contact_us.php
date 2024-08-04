<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
require 'send_email.php';



header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Access-Control-Allow-Credentials: true');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);

$response = [];

$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
$phoneNumber = filter_input(INPUT_POST, 'phoneNumber', FILTER_SANITIZE_STRING);
$message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

if ($name && !empty($name)) {
        $name = trim($_POST['name']);
        $response['status'] = 'success';
        $response['message'] = 'Name received.';
    } else {
         http_response_code(400); 
        $response['status'] = 'error';
        $response['message'] = 'Name is required and cannot be empty.';
        echo json_encode($response);
        return;
    }




$templateFile = 'contact_us.html';
$htmlContent = file_get_contents($templateFile);
if ($htmlContent === false) {
    die('Failed to read email template file.');
}

$pdf_file = 'contact_us_pdf.html';
$pdf_content = file_get_contents($pdf_file);
if ($pdf_content === false) {
    die('Failed to read email template file.');
}

$pdf_content = str_replace('{{name}}', $name, $pdf_content);
$pdf_content = str_replace('{{email}}', $email, $pdf_content);
$pdf_content = str_replace('{{phoneNumber}}', $phoneNumber, $pdf_content);
$pdf_content = str_replace('{{message}}', $message, $pdf_content);
$today = str_replace(date('F'), strtoupper(date('F')), date('d F Y'));
$pdf_content = str_replace('{{today}}', $today, $pdf_content);
    

$htmlContent = str_replace('{{today}}', $today, $htmlContent);
$htmlContent = str_replace('{{name}}', $name, $htmlContent);
$htmlContent = str_replace('{{email}}', $email, $htmlContent);
$htmlContent = str_replace('{{phoneNumber}}', $phoneNumber, $htmlContent);
$htmlContent = str_replace('{{message}}', $message, $htmlContent);

// send_email('contract us', 'hosamameen948@gmail.com', $htmlContent, $pdf_content, 'contract us', $name);
send_email('contract us', 'AbdooTawfeek@gmail.com', $htmlContent, $pdf_content, 'contract us', $name);


?>
