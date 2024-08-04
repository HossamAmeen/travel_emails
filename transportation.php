<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
require 'send_email.php';
require 'allow_cors.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);

$response = [];

$travelerName = filter_input(INPUT_POST, 'travelerName', FILTER_SANITIZE_STRING);

if ($travelerName && !empty($travelerName)) {
        $response['status'] = 'success';
        $response['message'] = 'Name received.';
    } else {
         http_response_code(400); 
        $response['status'] = 'error';
        $response['message'] = 'travelerName is required and cannot be empty.';
        echo json_encode($response);
        return;
    }

$templateFile = 'transportation.html';
$htmlContent = file_get_contents($templateFile);
if ($htmlContent === false) {
    die('Failed to read email template file.');
}

$travelerContactNumber = filter_input(INPUT_POST, 'travelerContactNumber', FILTER_SANITIZE_STRING);
$aircraftRegistration = filter_input(INPUT_POST, 'aircraftRegistration', FILTER_SANITIZE_STRING);
$flightCallSign = filter_input(INPUT_POST, 'flightCallSign', FILTER_SANITIZE_STRING);
$pickupLocation = filter_input(INPUT_POST, 'pickupLocation', FILTER_SANITIZE_STRING);
$dropOffLocation = filter_input(INPUT_POST, 'dropOffLocation', FILTER_SANITIZE_STRING);
$pickupDateTime = filter_input(INPUT_POST, 'pickupDateTime', FILTER_SANITIZE_STRING);
$carType = filter_input(INPUT_POST, 'carType', FILTER_SANITIZE_STRING);
$comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_STRING);


$htmlContent = str_replace('{{today}}', $today, $htmlContent);
$htmlContent = str_replace('{{travelerName}}', $travelerName, $htmlContent);
$htmlContent = str_replace('{{travelerContactNumber}}', $travelerContactNumber, $htmlContent);
$htmlContent = str_replace('{{aircraftRegistration}}', $aircraftRegistration, $htmlContent);
$htmlContent = str_replace('{{flightCallSign}}', $flightCallSign, $htmlContent);
$htmlContent = str_replace('{{pickupLocation}}', $pickupLocation, $htmlContent);
$htmlContent = str_replace('{{dropOffLocation}}', $dropOffLocation, $htmlContent);
$htmlContent = str_replace('{{pickupDateTime}}', $pickupDateTime, $htmlContent);
$htmlContent = str_replace('{{carType}}', $carType, $htmlContent);
$htmlContent = str_replace('{{comment}}', $comment, $htmlContent);


$pdf_content = $htmlContent;

$file_name = "Ground_handling";




// send_email('Transportation', 'hosamameen948@gmail.com', $htmlContent, $pdf_content, $file_name, $travelerName);
send_email('Transportation', 'AbdooTawfeek@gmail.com', $htmlContent, $pdf_content, $file_name, $travelerName);

    

?>
