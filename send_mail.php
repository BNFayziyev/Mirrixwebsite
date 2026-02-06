<?php
// send_mail.php

header('Content-Type: application/json');

// 1. Configuration
$recipient_email = "info@mirrixcorp.com"; // UPDATE THIS to your real email
$subject_prefix = "[Website Quote Request]";

// 2. Check if the request is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 3. Collect and Sanitize Input
    $name    = strip_tags(trim($_POST["name"]));
    $company = strip_tags(trim($_POST["company"]));
    $email   = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $phone   = strip_tags(trim($_POST["phone"]));
    $message = strip_tags(trim($_POST["message"]));

    // 4. Validation
    $errors = [];
    if (empty($name)) $errors[] = "Name is required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format.";
    if (empty($message)) $errors[] = "Message is required.";

    // 5. Send Email if no errors
    if (empty($errors)) {
        $email_content = "Name: $name\n";
        $email_content .= "Company: $company\n";
        $email_content .= "Email: $email\n";
        $email_content .= "Phone: $phone\n\n";
        $email_content .= "Message:\n$message\n";

        $headers = "From: $name <$email>";

        if (mail($recipient_email, "$subject_prefix $company", $email_content, $headers)) {
            echo json_encode(["status" => "success", "message" => "Request sent successfully."]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Server failed to send email."]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => implode(" ", $errors)]);
    }
} else {
    http_response_code(403);
    echo json_encode(["status" => "error", "message" => "Method not allowed."]);
}
?>