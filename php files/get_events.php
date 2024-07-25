<?php

// Database connection parameters
$servername = "localhost";
$username = "root"; // Replace with your MySQL username
$password = ""; // Replace with your MySQL password
$dbname = "pink_lunary"; // Replace with your MySQL database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Select all events from the events table
$sql = "SELECT * FROM log_info";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Initialize an empty array to store events data
    $events = array();

    // Loop through each row in the result set
    while ($row = $result->fetch_assoc()) {
        // Extract event details
        $event = array(
            "team_one" => $row["TEAMONE"],
            "team_one_score" => $row["TeamOneScore"],
            "team_two" => $row["TEAMTWO"],
            "team_two_score" => $row["TeamTwoScore"],
            "date" => $row["DATE"],
            "time" => $row["TIME"],
            "location" => $row["LOCATION"]
        );

        // Add the event to the events array
        $events[] = $event;
    }

    // Send JSON response containing events data
    header('Content-Type: application/json');
    echo json_encode($events);
} else {
    // If no events found, send empty JSON response
    echo json_encode(array());
}

// Close connection
$conn->close();

?>
