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

$travelerName = $_POST['travelerName'];

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

$travelerContactNumber = $_POST['travelerContactNumber'];
$aircraftRegistration = $_POST['aircraftRegistration'];
$flightCallSign = $_POST['flightCallSign'];
$pickupLocation = $_POST['pickupLocation'];
$dropOffLocation = $_POST['dropOffLocation'];
$pickupDateTime = $_POST['pickupDateTime'];
$carType = $_POST['carType'];
$comment = $_POST['comment'];

$today = str_replace(date('F'), strtoupper(date('F')), date('d F Y'));

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
send_email('Transportation', 'Ops@whitecloudsaviation.com', $htmlContent, $pdf_content, $file_name, $travelerName);

    

?>
