<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
require 'send_email.php';
require 'allow_cors.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



$response = [];

$operatorName = filter_input(INPUT_POST, 'operatorName', FILTER_SANITIZE_STRING);

if ($operatorName && !empty($operatorName)) {
        $response['status'] = 'success';
        $response['message'] = 'Name received.';
    } else {
        http_response_code(400); 
        $response['status'] = 'error';
        $response['message'] = 'operatorName is required and cannot be empty.';
        echo json_encode($response);
        return;
    }

$templateFile = 'ground_handling.html';
$htmlContent = file_get_contents($templateFile);
if ($htmlContent === false) {
    die('Failed to read email template file.');
}

$pdf_file = 'ground_handling_pdf.html';
$pdf_content = file_get_contents($pdf_file);
if ($pdf_content === false) {
    die('Failed to read email template file.');
}



$operatorEmail = filter_input(INPUT_POST, 'operatorEmail', FILTER_SANITIZE_STRING);
$operatorPhone = filter_input(INPUT_POST, 'operatorPhone', FILTER_SANITIZE_STRING);
$operatorWebsite = filter_input(INPUT_POST, 'operatorWebsite', FILTER_SANITIZE_STRING);
$aircraftRegistration = filter_input(INPUT_POST, 'aircraftRegistration', FILTER_SANITIZE_STRING);
$flightCallSign = filter_input(INPUT_POST, 'flightCallSign', FILTER_SANITIZE_STRING);
$aircraftType = filter_input(INPUT_POST, 'aircraftType', FILTER_SANITIZE_STRING);
$comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_STRING);

$file_name = "ground_handling";

$file_name = strtolower($file_name);
if (!is_dir($file_name)) {
    mkdir($file_name, 0777, true);
}
    
$user_name = strtolower($operatorName);
if (!is_dir($file_name . '/' .$user_name)) {
    mkdir($file_name . '/' . $user_name, 0777, true);
}

$lastSlashPos = strrpos($_SERVER['REQUEST_URI'] , '/');
$baseUrl = substr($_SERVER['REQUEST_URI'], 0, $lastSlashPos + 1);

if (isset($_FILES['fuelRelease'])) {
    $fuel_release_file = $_FILES['fuelRelease'];
    $fuel_dest_path = $file_name . '/' . $user_name . '/' . date('Ymd_His') . "_" . $fuel_release_file['name']; 
    if(move_uploaded_file($fuel_release_file['tmp_name'], $fuel_dest_path)) {
        $fuelRelease = $_SERVER['HTTP_HOST'] . $baseUrl . '/' . $fuel_dest_path;
    }else{
        http_response_code(400); 
        $response['status'] = 'error';
        $response['message'] = 'there is issue with fuelRelease .';
        echo json_encode($response);
        return;
    }
    
}else{
    http_response_code(400); 
    $response['status'] = 'error';
    $response['message'] = 'fuelRelease is required and should be file.' . $_FILES['fuelRelease']['error'];
    echo json_encode($response);
    return;
}

if (isset($_FILES['crewDocument']) && $_FILES['crewDocument']['error'] == 0) {
    $crew_document_file = $_FILES['crewDocument'];
    $crew_document_file_dest_path = $file_name . '/' . $user_name . '/' . date('Ymd_His') . "_" . $crew_document_file['name']; 
    if(move_uploaded_file($crew_document_file['tmp_name'], $crew_document_file_dest_path)) {
        $crewDocument = $_SERVER['HTTP_HOST'] . $baseUrl . '/' . $crew_document_file_dest_path;
    }else{
        http_response_code(400); 
        $response['status'] = 'error';
        $response['message'] = 'there is issue with fuelRelease .';
        echo json_encode($response);
        return;
    }
}else{
    http_response_code(400); 
    $response['status'] = 'error';
    $response['message'] = 'crewDocument is required and should be file.';
    echo json_encode($response);
    return;
}


############################## zip
$files = [$crew_document_file_dest_path, $fuel_dest_path];


if (!is_dir('uploads')) {
  mkdir('uploads', 0777, true);
}

if (!is_dir('uploads/zips')) {
  mkdir('uploads/zips', 0777, true);
}

$zipFileName = "uploads/zips/" . $file_name . "_" . $operatorName . "_" .  date('Ymd_His') . '.zip';

$zip = new ZipArchive();
if ($zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
    exit("Unable to create zip file $zipFileName\n");
}

// Add files to the zip archive
foreach ($files as $file) {
    if (file_exists($file)) {
        $zip->addFile($file, ($file));
    } else {
        exit("File $file does not exist\n");
    }
}

$zip->close();
$zipFileName = $_SERVER['HTTP_HOST'] . $baseUrl . '/' . $zipFileName;


$sector_code_item_temp = '
<div class="sectors">
          <div class="header-section" style="margin-top: 30px">
            <span style="
                  display: block;
                  font-size: 25px;
                  font-weight: 600;
                  margin-bottom: 20px;
                ">Flight Sectors - {{sectorCount}}</span>
            <hr style="
                  max-width: 80px;
                  height: 4px;
                  border-color: #bda55d;
                  background-color: #bda55d;
                  border-radius: 12px;
                  margin: 1px;
                " />
          </div>
          <div class="form-data" style="margin: 20px 0px 40px">
            <div class="title" style="margin-bottom: 18px; font-size: 18px; font-weight: 200">
              <span style="
                    display: block;
                    margin-bottom: 10px;
                    color: #bda55d;
                    font-weight: 500;
                  ">Departure Airport:</span>
              {{departureAirport}}
            </div>
            <div class="title" style="margin-bottom: 18px; font-size: 18px; font-weight: 200">
              <span style="
                    display: block;
                    margin-bottom: 10px;
                    color: #bda55d;
                    font-weight: 500;
                  ">Arrival Airport:</span>
              XXXXXX
            </div>
            <div class="title" style="margin-bottom: 18px; font-size: 18px; font-weight: 200">
              <span style="
                    display: block;
                    margin-bottom: 10px;
                    color: #bda55d;
                    font-weight: 500;
                  ">Departure Date & Time:</span>
              XXXXXX
            </div>
            <div class="title" style="margin-bottom: 18px; font-size: 18px; font-weight: 200">
              <span style="
                    display: block;
                    margin-bottom: 10px;
                    color: #bda55d;
                    font-weight: 500;
                  ">Arrival Date & Time:</span>
              XXXXXX
            </div>
            <div class="title" style="margin-bottom: 18px; font-size: 18px; font-weight: 200">
              <span style="
                    display: block;
                    margin-bottom: 10px;
                    color: #bda55d;
                    font-weight: 500;
                  ">Departure Airport:</span>
              XXXXXX
            </div>
            <div class="title" style="margin-bottom: 18px; font-size: 18px; font-weight: 200">
              <span style="
                    display: block;
                    margin-bottom: 10px;
                    color: #bda55d;
                    font-weight: 500;
                  ">Arrival Airport:</span>
              XXXXXX
            </div>
            <div class="title" style="margin-bottom: 18px; font-size: 18px; font-weight: 200">
              <span style="
                    display: block;
                    margin-bottom: 10px;
                    color: #bda55d;
                    font-weight: 500;
                  ">Departure Date & Time:</span>
              XXXXXX
            </div>
            <div class="title" style="margin-bottom: 18px; font-size: 18px; font-weight: 200">
              <span style="
                    display: block;
                    margin-bottom: 10px;
                    color: #bda55d;
                    font-weight: 500;
                  ">Arrival Date & Time:</span>
              XXXXXX
            </div>
          </div>
        </div>';

$sector_count = 1;
$sectors = $_POST['sectors'];
$sector_code = '';
if (!isset($_POST['sectors']) && !is_array($_POST['sectors'])) {
  http_response_code(400); // Set HTTP response code to 400 Bad Request
  echo json_encode(['status' => 'error', 'message' => 'Sectors must be an array']);
  return;
}
foreach ($sectors as $sector) {
    // echo  $sectors;
    echo gettype($sector) . "<br>";
    $sector = json_decode($sector, true);
    $sector_code_item = str_replace('{{sectorCount}}', $sector_count, $sector_code_item_temp);
    $sector_code_item = str_replace('{{departureAirport}}', $sector['departureAirport'], $sector_code_item);
    $sector_count++;
    $sector_code = $sector_code. $sector_code_item;
}

$pdf_content = str_replace('{{operatorName}}', $operatorName, $pdf_content);
$pdf_content = str_replace('{{operatorEmail}}', $operatorEmail, $pdf_content);
$pdf_content = str_replace('{{operatorPhone}}', $operatorPhone, $pdf_content);
$pdf_content = str_replace('{{operatorWebsite}}', $operatorWebsite, $pdf_content);
$pdf_content = str_replace('{{aircraftRegistration}}', $aircraftRegistration, $pdf_content);
$pdf_content = str_replace('{{flightCallSign}}', $flightCallSign, $pdf_content);
$pdf_content = str_replace('{{aircraftType}}', $aircraftType, $pdf_content);
$pdf_content = str_replace('{{comment}}', $comment, $pdf_content);
$pdf_content = str_replace('{{sector_code}}', $sector_code, $pdf_content);
$today = str_replace(date('F'), strtoupper(date('F')), date('d F Y'));
$pdf_content = str_replace('{{today}}', $today, $pdf_content);

$htmlContent = str_replace('{{operatorName}}', $operatorName, $htmlContent);
$htmlContent = str_replace('{{operatorEmail}}', $operatorEmail, $htmlContent);
$htmlContent = str_replace('{{operatorPhone}}', $operatorPhone, $htmlContent);
$htmlContent = str_replace('{{operatorWebsite}}', $operatorWebsite, $htmlContent);
$htmlContent = str_replace('{{aircraftRegistration}}', $aircraftRegistration, $htmlContent);
$htmlContent = str_replace('{{flightCallSign}}', $flightCallSign, $htmlContent);
$htmlContent = str_replace('{{aircraftType}}', $aircraftType, $htmlContent);
$htmlContent = str_replace('{{fuelRelease}}', $fuelRelease, $htmlContent);
$htmlContent = str_replace('{{crewDocument}}', $crewDocument, $htmlContent);
$htmlContent = str_replace('{{comment}}', $comment, $htmlContent);
$htmlContent = str_replace('{{sector_code}}', $sector_code, $htmlContent);
$htmlContent = str_replace('{{zip_url}}', $zipFileName, $htmlContent);

send_email('Ground handling', 'hosamameen948@gmail.com', $htmlContent, $pdf_content, $file_name, $operatorName);
// send_email('Ground handling', 'AbdooTawfeek@gmail.com' , $htmlContent, $pdf_content, $file_name, $operatorName);
    

?>
