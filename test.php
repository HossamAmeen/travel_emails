<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
$mail = new PHPMailer(true);
try {

    $mail->isSMTP();
    $mail->Host       = 'smtp.aboadam-used.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'learn@aboadam-used.com';
    $mail->Password   = 'f~COLZ4UaKe';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = 465;

    // Recipients
    $mail->setFrom('learn@aboadam-used.com', 'Sender Name');
    $mail->addAddress("hosamameen948@gmail.com", 'Recipient Name');

    $mail->isHTML(true);  // Set email format to HTML
    $mail->Subject = "test";

    $mail->Body = "test";
    
    $mail->send();
    $response['status'] = 'success';
    $response['message'] = 'Message has been sent';
    echo json_encode($response);
        return;
} catch (Exception $e) {
    http_response_code(400);
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
?>