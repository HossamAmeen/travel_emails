<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Predis\Client;
require 'vendor/autoload.php';

header("Content-Type: application/json");


function send_email($subject, $reciever, $body, $pdf_body, $file_name, $user_name="Sender Name"){
    $redis = new Client();
    // Job data
    $job = [
        'type' => 'send_welcome_email',
        'data' => [
            'subject' => $subject,
            "reciever" => $reciever,
            "body" => $body
        ]
    ];
    $redis->rpush('email_queue', json_encode($job));
    return
    $mail = new PHPMailer(true);
    try {

        $mail->isSMTP();
        $mail->Host       = 'smtp.office365.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'admin@whitecloudsaviation.com';
        $mail->Password   = 'Admin$123123';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('admin@whitecloudsaviation.com', $user_name);
        $mail->addAddress($reciever, 'Recipient Name');

        $mail->isHTML(true);  // Set email format to HTML
        $mail->subject = $subject;

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
        http_response_code(400);
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>