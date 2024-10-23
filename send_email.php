<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

header("Content-Type: application/json");


function send_email($Subject, $reciever, $body, $pdf_body, $file_name, $user_name="test"){
    $mail = new PHPMailer(true);
    try {

        $mail->isSMTP();
        $mail->Host       = 'smtp.aboadam-used.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'learn@aboadam-used.com';
        $mail->Password   = '5~;CG6r[U2Jb';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // Recipients
        $mail->setFrom('learn@aboadam-used.com', 'Sender Name');
        $mail->addAddress($reciever, 'Recipient Name');

        $mail->isHTML(true);  // Set email format to HTML
        $mail->Subject = $Subject;

        $mail->Body = $body;

        $mail->addCC('hosamameen948@gmail.com', 'CC Recipient');
        $mail->addCC('mahmoudyasser11548@gmail.com', 'CC Recipient');
        $mail->addCC('AbdooTawfeek@gmail.com', 'CC Recipient');
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