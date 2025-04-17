<?php

$servername = "localhost";
$username = "root"; 
$password = "";      
$dbname = "wanderlust";  

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = htmlspecialchars(trim($_POST["name"]));
    $email    = filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL);
    $phone    = htmlspecialchars(trim($_POST["phone"]));
    $subject  = htmlspecialchars(trim($_POST["subject"]));
    $message  = htmlspecialchars(trim($_POST["message"]));

    if (!$name || !$email || !$subject || !$message) {
        die("Please fill out all required fields correctly.");
    }

    $stmt = $conn->prepare("INSERT INTO contact_form (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sssss", $name, $email, $phone, $subject, $message);

        if ($stmt->execute()) {
            echo "Thank you! Your message has been sent successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error: " . $conn->error;
    }

    $conn->close();
} else {
    echo "Invalid request.";
}
?>


