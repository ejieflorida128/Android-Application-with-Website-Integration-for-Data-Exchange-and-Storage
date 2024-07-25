<?php

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pink_lunary";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve JSON data sent from Android application
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Loop through each event in the JSON data and process it
foreach ($data as $event) {
    // Extract event details
    $team_one = $event['team_one'];
    $team_one_score = $event['team_one_score'];
    $team_two = $event['team_two'];
    $team_two_score = $event['team_two_score'];
    $date = $event['date'];
    $time = $event['time'];
    $location = $event['location'];

    // Prepare SQL statement to insert or update event in the database
    $sql = "INSERT INTO log_info (TEAMONE, TeamOneScore, TEAMTWO, TeamTwoScore, DATE, TIME, LOCATION)
            VALUES ('$team_one', '$team_one_score', '$team_two', '$team_two_score', '$date', '$time', '$location')
            ON DUPLICATE KEY UPDATE
            TeamOneScore = '$team_one_score',
            TeamTwoScore = '$team_two_score',
            DATE = '$date',
            TIME = '$time',
            LOCATION = '$location'";

    // Execute SQL statement
    if ($conn->query($sql) !== TRUE) {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Close database connection
$conn->close();

?>
