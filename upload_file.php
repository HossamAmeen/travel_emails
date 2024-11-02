<?php
header('Content-Type: application/json');

// Function to respond with JSON
function respond($status, $message, $data = null) {
    http_response_code($status);
    echo json_encode(['message' => $message, 'data' => $data]);
    exit();
}

// Check if a file has been uploaded
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];

    // Check for errors
    if ($file['error'] === UPLOAD_ERR_OK) {
        // Get the current year and month
        $year = date('Y');
        $month = date('m');

        // Create the directory if it doesn't exist
        if (!is_dir("uploads")) {
            mkdir("uploads", 0777, true);
        }
        $uploadDir = "uploads/$year";
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true)) {
                respond(500, 'Failed to create year directories.');
            }
            $uploadDir = "uploads/$year/$month";
            if (!mkdir($uploadDir, 0777, true)) {
                respond(500, 'Failed to create month directories.');
            }
        }

        // Get the current time and original filename
        $currentTime = date('H-i-s');
        $originalFileName = basename($file['name']);

        // Generate the new filename
        $newFileName = "$currentTime-$originalFileName";
        $destination = "$uploadDir/$newFileName";

        // Move the uploaded file to the new location
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            $baseUrl = $_SERVER['HTTP_HOST'] . substr($_SERVER['REQUEST_URI'], 0, strrpos($_SERVER['REQUEST_URI'] , '/') + 1);

            respond(200, 'File uploaded successfully.', $baseUrl . '/' .$destination);
        } else {
            respond(500, 'Failed to move uploaded file.');
        }
    } else {
        respond(400, 'File upload error: ' . $file['error']);
    }
} else {
    respond(400, 'No file uploaded.');
}
?>
