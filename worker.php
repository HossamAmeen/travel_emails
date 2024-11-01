<?php
require 'vendor/autoload.php';

use Predis\Client;

function processJob($job) {
    // Decode the job data
    $job = json_decode($job, true);

    // Check the type of job
    if ($job['type'] === 'send_welcome_email') {
        $email = $job['data']['email'];
        $title = $job['data']['title'];
        echo $job['data']['body'];
        sendWelcomeEmail($email, $title);
    }
}

function sendWelcomeEmail($email, $title="test") {
    // Simulate sending an email
    echo "Sending welcome email to $email\n";
    echo $title;
    // Here you would actually send the email using a mailer library like PHPMailer, SwiftMailer, etc.

}

$redis = new Client();

while (true) {
    // Block until a job is available
    $job = $redis->blpop('email_queue', 0);

    // Process the job
    processJob($job[1]);

    // Sleep for a short time to avoid busy waiting (optional)
    usleep(500000); // 0.5 seconds
}
