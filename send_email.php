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
            "body" => $body,
            "user_name" => $user_name
        ]
    ];
    $redis->rpush('email_queue', json_encode($job));
    $response['status'] = 'success';
    $response['message'] = 'Message has been sent';
    echo json_encode($response);
    
}
?>