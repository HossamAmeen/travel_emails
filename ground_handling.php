<?php

require 'vendor/autoload.php';
require 'send_email.php';
require 'allow_cors.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);

use Twig\Environment;
use Twig\Loader\FilesystemLoader;


function upload_file($upload_name, $file, $file_name){
    $path = "uploads/$file_name" . date('Ymd_His')  . rand(1,10) . "_" . $file['name']; 
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
$file_name = "ground_handling";

if (!is_dir('uploads')) {
    mkdir('uploads', 0777, true);
  }
  if (!is_dir("uploads/" . $file_name)) {
    mkdir("uploads/" . $file_name, 0777, true);
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

if (isset($_FILES['fuelRelease'])) {
    $_POST['fuelRelease']  = upload_file('fuelRelease', $_FILES['fuelRelease'], $file_name);
}else{
    
    $startPos = strpos($_POST['fuelRelease'], "uploads");

    if ($startPos !== false) {
        $_POST['fuelRelease'] = substr($_POST['fuelRelease'], $startPos);
    }
}

if (isset($_FILES['crewDocument'])) {
    $_POST['crewDocument']  = upload_file('crewDocument', $_FILES['crewDocument'], $file_name);
}else{
    $startPos = strpos($_POST['crewDocument'], "uploads");

    if ($startPos !== false) {
        $_POST['crewDocument'] = substr($_POST['crewDocument'], $startPos);
    }
}

$files = [$_POST['crewDocument'], $_POST['fuelRelease']];

if (!is_dir('uploads/zips')) {
    mkdir('uploads/zips', 0777, true);
}
  

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
$baseUrl = $_SERVER['HTTP_HOST'] . substr($_SERVER['REQUEST_URI'], 0, strrpos($_SERVER['REQUEST_URI'] , '/') + 1);

$_POST['zip_url'] = $baseUrl . '/' . $zipFileName;
$_POST['download_link'] =  $baseUrl . '/' . $pdf_path;
$_POST['crewDocument'] = $baseUrl . '/' . $_POST['crewDocument'] ;
$_POST['fuelRelease'] = $baseUrl . '/' . $_POST['fuelRelease'] ;
$_POST['is_email'] = True;
$template_data =  $twig->render("$file_name.html.twig", $_POST);

send_email('Ground handling', 'Ops@whitecloudsaviation.com' , $template_data, $template_data, $file_name, $user_name);

?>