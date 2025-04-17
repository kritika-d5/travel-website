<?php
require_once 'db_connect.php';

function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $destination = $_POST['destination'] ?? '';
    $travelers = $_POST['travelers'] ?? '';
    $departure = $_POST['departure-date'] ?? '';
    $return = $_POST['return-date'] ?? '';
    $budget = $_POST['budget'] ?? '';
    $tripType = $_POST['trip-type'] ?? '';
    $specialRequests = $_POST['special-requests'] ?? '';
    $interests = implode(',', (array) ($_POST['interests'] ?? []));


    if (!$departure || !$return || !validateDate($departure) || !validateDate($return)) {
        echo "Please select valid dates.";
        exit();
    }
    
    $stmt = $conn->prepare("INSERT INTO trip_plans 
        (destination, travelers, departure_date, return_date, budget, trip_type, interests, special_requests) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("sissssss", $destination, $travelers, $departure, $return, $budget, $tripType, $interests, $specialRequests);

    if ($stmt->execute()) {
        // Success message
        echo "<script type='text/javascript'>alert('Trip Plan Created Successfully!'); window.location.href='plan-trip.html';</script>";
    } else {
        // Error message
        echo "<script type='text/javascript'>alert('Error: " . $stmt->error . "');</script>";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
