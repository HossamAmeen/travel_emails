<?php
require 'vendor/autoload.php';

use Predis\Client;

function enqueueWelcomeEmail($email, $title) {
    $redis = new Client();

    // Job data
    $job = [
        'type' => 'send_welcome_email',
        'data' => [
            'email' => $email,
            "title" => $title
        ]
    ];

    // Push the job onto the 'email_queue'
    $redis->rpush('email_queue', json_encode($job));
}

// Get the email from the request
$email = isset($_POST['email']) ? $_POST['email'] : null;

if ($email) {
    enqueueWelcomeEmail($email, "test title test");
    echo "Job enqueued to send welcome email to $email\n";
} else {
    echo "Email address is required.\n";
}
