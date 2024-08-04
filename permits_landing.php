<?php
require 'vendor/autoload.php';
require 'send_email.php';
require 'allow_cors.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);

use Twig\Environment;
use Twig\Loader\FilesystemLoader;


function upload_file($upload_name, $file){
    
        $path = 'uploads/' . date('Ymd_His')  . rand(1,10) . "_" . $file['name']; 
        if(move_uploaded_file($file['tmp_name'], $path)) {
            $lastSlashPos = strrpos($_SERVER['REQUEST_URI'] , '/');
            $baseUrl = substr($_SERVER['REQUEST_URI'], 0, $lastSlashPos + 1);
            $path = $_SERVER['HTTP_HOST'] . $baseUrl . '/' . $path;
            return $path;
        }else{
            http_response_code(400); 
            $response['status'] = 'error';
            $response['message'] = 'there is issue with ' .  $upload_name . ' ' . $file['error'];
            echo json_encode($response);
            return;
        }
        
    
}

$file_name = "permits_landing.html.twig";
$user_name = $_POST['operatorName'];

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


if (isset($_FILES['certInsurance'])) {
    $_POST['certInsurance']  = upload_file('certInsurance', $_FILES['certInsurance']);
}else{
    http_response_code(400); 
    $response['status'] = 'error';
    $response['message'] = 'certInsurance is required and should be file.';
    echo json_encode($response);
    return;
}

if (isset($_FILES['airworthiness'])) {
    $_POST['airworthiness']  = upload_file('airworthiness', $_FILES['airworthiness']);
}else{
    http_response_code(400); 
    $response['status'] = 'error';
    $response['message'] = 'airworthiness is required and should be file.';
    echo json_encode($response);
    return;
}

if (isset($_FILES['noise'])) {
    $_POST['noise']  = upload_file('noise', $_FILES['noise']);
}else{
    http_response_code(400); 
    $response['status'] = 'error';
    $response['message'] = 'noise is required and should be file.';
    echo json_encode($response);
    return;
}

if (isset($_FILES['certRegistration'])) {
    $_POST['certRegistration']  = upload_file('certRegistration', $_FILES['certRegistration']);
}else{
    http_response_code(400); 
    $response['status'] = 'error';
    $response['message'] = 'certRegistration is required and should be file.';
    echo json_encode($response);
    return;
}

if (isset($_FILES['radioLicense'])) {
    $_POST['radioLicense']  = upload_file('radioLicense', $_FILES['radioLicense']);
}else{
    http_response_code(400); 
    $response['status'] = 'error';
    $response['message'] = 'radioLicense is required and should be file.';
    echo json_encode($response);
    return;
}




// Create a Twig environment
$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader, [
    // 'cache' => __DIR__ . '/cache',
]);

$today = str_replace(date('F'), strtoupper(date('F')), date('d F Y'));

$_POST['today'] = $today;
$_POST['is_email'] = False;

$template_data =  $twig->render('permits_landing.html.twig', $_POST);


$mpdf = new \Mpdf\Mpdf(['default_font' => 'dejavusans']);
$mpdf->WriteHTML($template_data);

$pdf_path = "uploads/" . $file_name. '/' . $user_name . '/' . '_' . date('Ymd_His'). '.pdf';
$mpdf->Output($pdf_path, 'F'); 


$lastSlashPos = strrpos($_SERVER['REQUEST_URI'] , '/');
$baseUrl = substr($_SERVER['REQUEST_URI'], 0, $lastSlashPos + 1);
$downloadLink = $_SERVER['HTTP_HOST'] . $baseUrl . '/' . $pdf_path;
$_POST['download_link'] = $downloadLink;
$_POST['is_email'] = True;

$template_data =  $twig->render('permits_landing.html.twig', $_POST);

send_email('permits landing', 'AbdooTawfeek@gmail.com' , $template_data, $template_data, $file_name, $user_name);


?>