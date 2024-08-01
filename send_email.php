<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

header("Content-Type: application/json");


function send_email($Subject, $reciever, $body, $pdf_body, $file_name, $user_name="test"){



$mail = new PHPMailer(true);

try {

    $mail->isSMTP();
    $mail->Host       = 'smtp.egypal.fr';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'learn@egypal.fr';
    $mail->Password   = '5~;CG6r[U2Jb';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = 465;

    // Recipients
    $mail->setFrom('learn@egypal.fr', 'Sender Name');
    $mail->addAddress($reciever, 'Recipient Name');

    $mail->isHTML(true);  // Set email format to HTML
    $mail->Subject = $Subject;

    $today = date('d F Y');
    $today = str_replace(date('F'), strtoupper(date('F')), $today);

    $body = str_replace('{{today}}', $today, $body);
    
    # create pdf
    $mpdf = new \Mpdf\Mpdf(['default_font' => 'dejavusans']);
    $mpdf->WriteHTML($pdf_body);
    
    $file_name = strtolower($file_name);
    if (!is_dir($file_name)) {
        mkdir($file_name, 0777, true);
    }
    
    $user_name = strtolower($user_name);
    if (!is_dir($file_name . '/' .$user_name)) {
        mkdir($file_name . '/' . $user_name, 0777, true);
    }
    $file_path = $file_name. '/' . $user_name . '/' . $file_name . '_' . date('Ymd_His'). '.pdf';
    $mpdf->Output($file_path, 'F'); 

    $lastSlashPos = strrpos($_SERVER['REQUEST_URI'] , '/');
    $baseUrl = substr($_SERVER['REQUEST_URI'], 0, $lastSlashPos + 1);
    $downloadLink = $_SERVER['HTTP_HOST'] . $baseUrl . '/' . $file_path;
    $body = str_replace('{{downloadLink}}', $downloadLink, $body);


    $mail->Body = $body;

    $mail->addCC('hosamameen948@gmail.com', 'CC Recipient');
    $mail->SMTPOptions = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true,
        ),
    );
    $mail->send();
    $response['status'] = 'success';
    $response['message'] = 'Message has been sent';
    echo json_encode($response);
        return;
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
}
?>