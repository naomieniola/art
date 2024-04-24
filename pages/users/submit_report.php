<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get comment ID and report text
    $commentId = $_POST["commentId"];
    $reportText = $_POST["reportText"];

    // Send email (replace this with your email sending logic)
    $to = "naomiadesiyan@hotmail.com";
    $subject = "Report on Comment ID: $commentId";
    $message = "Report Text:\n$reportText";
    $headers = "From: webmaster@example.com"; // Replace with your email or leave it empty

    mail($to, $subject, $message, $headers);

    // You can add additional logic or response as needed
    echo "Report submitted successfully";
} else {
    // Handle invalid request method
    http_response_code(405);
    echo "Method Not Allowed";
}
?>