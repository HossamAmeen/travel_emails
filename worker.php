<?php
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Predis\Client;

function processJob($job) {
    // Decode the job data
    $job = json_decode($job, true);

    // Check the type of job
    if ($job['type'] === 'send_welcome_email') {
        $subject = $job['data']['subject'];
        $reciever = $job['data']['reciever'];
        $body  = $job['data']['body'];
        $user_name  = $job['data']['user_name'];
        sendWelcomeEmail($subject, $reciever, $body, $user_name);
    }
}

function sendWelcomeEmail($subject, $reciever, $body, $user_name) {
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
        // $mail->addAddress($reciever, 'Recipient Name');
        $mail->addAddress("hosamameen948@gmail.com", 'Recipient Name');
        
        $mail->isHTML(true);  // Set email format to HTML
        $mail->Subject = $subject;

        $mail->Body = $body;

        $mail->addCC('hosamameen948@gmail.com', 'CC Recipient');
        // $mail->addCC('mahmoudyasser11548@gmail.com', 'CC Recipient');
        // $mail->addCC('AbdooTawfeek@gmail.com', 'CC Recipient');
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

try {
    $redis = new Client([
        'scheme' => 'tcp',
        'host' => '127.0.0.1',
        'port' => 6379,
    ]);

    while (true) {
        // Try to pop a job from the list
        $job = $redis->rpop('email_queue');

        if ($job) {
            // Process the job if available
            processJob($job);
        } else {
            // Sleep for a short time if no job is available
            usleep(500000); // 0.5 seconds
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
