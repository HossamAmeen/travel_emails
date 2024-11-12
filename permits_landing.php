<?php
require 'vendor/autoload.php';
require 'send_email.php';
require 'allow_cors.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);

use Twig\Environment;
use Twig\Loader\FilesystemLoader;


$file_name = "permits_landing";

function upload_file($upload_name, $file){
        $path = "uploads/$file_name" . date('Ymd_His')  . rand(1,10) . "_" . $file['name']; 
        echo $path;
        if(move_uploaded_file($file['tmp_name'], $path)) {
            return $path;
        }else{
            http_response_code(400); 
            $response['status'] = 'error';
            $response['message'] = 'there is issue with ' .  $upload_name . ' ' . $file['error'];
            echo json_encode($response);
            return;
        }
        
    
}



$user_name = $_POST['operatorName'];

if (!is_dir("uploads")) {
    mkdir("uploads", 0777, true);
}

if (!is_dir("uploads/" . $file_name)) {
    mkdir("uploads/" . $file_name, 0777, true);
}

if (!is_dir('uploads/zips')) {
    mkdir('uploads/zips', 0777, true);
}

if (isset($_FILES['certInsurance'])) {
    $_POST['certInsurance']  = upload_file('certInsurance', $_FILES['certInsurance']);
}else{
    $startPos = strpos($_POST['certInsurance'], "uploads");
    if ($startPos !== false) {
        $_POST['certInsurance'] = substr($_POST['certInsurance'], $startPos);
    }
}

if (isset($_FILES['airworthiness'])) {
    $_POST['airworthiness']  = upload_file('airworthiness', $_FILES['airworthiness']);
}else{
    $startPos = strpos($_POST['airworthiness'], "uploads");

    if ($startPos !== false) {
        $_POST['airworthiness'] = substr($_POST['airworthiness'], $startPos);
    }
}

if (isset($_FILES['noise'])) {
    $_POST['noise']  = upload_file('noise', $_FILES['noise']);
}else{
    $startPos = strpos($_POST['noise'], "uploads");

    if ($startPos !== false) {
        $_POST['noise'] = substr($_POST['noise'], $startPos);
    }
}

if (isset($_FILES['certRegistration'])) {
    $_POST['certRegistration']  = upload_file('certRegistration', $_FILES['certRegistration']);
}else{
    $startPos = strpos($_POST['certRegistration'], "uploads");

    if ($startPos !== false) {
        $_POST['certRegistration'] = substr($_POST['certRegistration'], $startPos);
    }
}

if (isset($_FILES['radioLicense'])) {
    $_POST['radioLicense']  = upload_file('radioLicense', $_FILES['radioLicense']);
}else{
    $startPos = strpos($_POST['radioLicense'], "uploads");

    if ($startPos !== false) {
        $_POST['radioLicense'] = substr($_POST['radioLicense'], $startPos);
    }
}

$baseUrl = $_SERVER['HTTP_HOST'] . substr($_SERVER['REQUEST_URI'], 0, strrpos($_SERVER['REQUEST_URI'] , '/') + 1);
$files = [$_POST['certInsurance'], $_POST['airworthiness'], $_POST['noise'],
          $_POST['certRegistration'] , $_POST['radioLicense']];

if (isset($_FILES['sectors'])){
    for ($i=0; $i < count($_FILES['sectors']['name']); $i++) {
        $crew_document_name = "uploads/$file_name/" . date('Ymd_His')  . rand(1,10) . "_" .$_FILES['sectors']['name'][$i]['crewDocument'];
        if(move_uploaded_file( $_FILES['sectors']['tmp_name'][$i]['crewDocument'], $crew_document_name))
            $_POST['sectors'][$i]['crewDocument'] = $baseUrl . $crew_document_name;
            array_push($files, $crew_document_name);
        $ground_handling_name = "uploads/$file_name" . date('Ymd_His')  . rand(1,10) . "_" .$_FILES['sectors']['name'][$i]['groundHandling'];
        if(move_uploaded_file( $_FILES['sectors']['tmp_name'][$i]['groundHandling'], $ground_handling_name))
            $_POST['sectors'][$i]['groundHandling'] = $baseUrl . $ground_handling_name;
            array_push($files, $ground_handling_name);
    }
}
// Create a Twig environment
$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader, [
    'cache' => __DIR__ . '/cache',
]);

$today = str_replace(date('F'), strtoupper(date('F')), date('d F Y'));

$_POST['today'] = $today;
$_POST['is_email'] = False;

$template_data =  $twig->render("$file_name.html.twig", $_POST);


$mpdf = new \Mpdf\Mpdf(['default_font' => 'dejavusans']);
$mpdf->WriteHTML($template_data);

$pdf_path = "uploads/" . $file_name ."/". $user_name  . '_' . date('Ymd_His'). '.pdf';
$mpdf->Output($pdf_path, 'F'); 


$zipFileName = "uploads/zips/" . $file_name . "_" . $user_name . "_" .  date('Ymd_His') . '.zip';

$zip = new ZipArchive();
if ($zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
    exit("Unable to create zip file $zipFileName\n");
}

array_push($files, $pdf_path);

// Add files to the zip archive
foreach ($files as $file) {
    if (file_exists($file)) {
        $zip->addFile($file, ($file));
    } else {
        exit("File $file does not exist\n");
    }
}

$zip->close();

$lastSlashPos = strrpos($_SERVER['REQUEST_URI'] , '/');
$baseUrl = substr($_SERVER['REQUEST_URI'], 0, $lastSlashPos + 1);
$downloadLink = $_SERVER['HTTP_HOST'] . $baseUrl . '/' . $pdf_path;

$_POST['zipFileName'] = $_SERVER['HTTP_HOST'] . $baseUrl . '/' . $zipFileName;
$_POST['certInsurance'] = $_SERVER['HTTP_HOST'] . $baseUrl . '/' . $_POST['certInsurance'];
$_POST['airworthiness'] = $_SERVER['HTTP_HOST'] . $baseUrl . '/' . $_POST['airworthiness'] ;
$_POST['noise'] = $_SERVER['HTTP_HOST'] . $baseUrl . '/' . $_POST['noise'];
$_POST['certRegistration'] = $_SERVER['HTTP_HOST'] . $baseUrl . '/' . $_POST['certRegistration'];
$_POST['radioLicense'] = $_SERVER['HTTP_HOST'] . $baseUrl . '/' . $_POST['radioLicense'];


$_POST['download_link'] = $downloadLink;
$_POST['is_email'] = True;

$template_data =  $twig->render("$file_name.html.twig", $_POST);

send_email('permits landing', 'Ops@whitecloudsaviation.com' , $template_data, $template_data, $file_name, $user_name);


?>