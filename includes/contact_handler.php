<?php
session_start();
// The path to config.php is correct because this file is in the same directory.
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input
    $name = trim(htmlspecialchars($_POST['name'] ?? ''));
    $email = trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));
    $subject = trim(htmlspecialchars($_POST['subject'] ?? ''));
    $message = trim(htmlspecialchars($_POST['message'] ?? ''));

    // Check for empty fields
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        // Redirect back with an error message
        header("Location: ../contact.php?status=error&msg=emptyfields");
        exit();
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Redirect back with an error message
        header("Location: ../contact.php?status=error&msg=invalidemail");
        exit();
    }

    // Prepare and bind the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $subject, $message);

    // Execute the statement and redirect based on the result
    if ($stmt->execute()) {
        // On success, redirect to the contact page with a success message
        header("Location: ../contact.php?status=success");
        exit();
    } else {
        // On failure, redirect with a database error message
        header("Location: ../contact.php?status=error&msg=dberror");
        exit();
    }

    $stmt->close();
    $conn->close();
} else {
    // If the page is accessed directly, not via POST, redirect to the contact page
    header("Location: ../contact.php");
    exit();
}
