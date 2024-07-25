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

// Delete all existing data from the users table
$sqlDelete = "DELETE FROM users";
if ($conn->query($sqlDelete) === TRUE) {
    echo "All existing data deleted successfully. ";
} else {
    echo "Error deleting existing data: " . $conn->error;
}

// Get the POST data
$data = json_decode(file_get_contents("php://input"), true);

// Check if data is received
if (!empty($data)) {
    foreach ($data as $user) {
        $userId = $user['userId'];
        $userName = $user['userName'];
        
        // Prepare and bind parameters
        $stmt = $conn->prepare("INSERT INTO users (Id, Name) VALUES (?, ?)");
        $stmt->bind_param("ss", $userId, $userName);

        // Execute the query
        if ($stmt->execute() === TRUE) {
            echo json_encode(array("status" => "success", "message" => "New record inserted successfully"));
        } else {
            echo json_encode(array("status" => "error", "message" => "Failed to insert new record"));
        }
    }
} else {
    echo json_encode(array("status" => "error", "message" => "No data received"));
}

// Close connection
$conn->close();
?>
