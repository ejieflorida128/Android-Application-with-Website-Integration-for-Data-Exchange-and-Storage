<?php
// Database connection parameters
$host = "localhost"; // Your host name
$username = "root"; // Your database username
$password = ""; // Your database password
$database = "pink_lunary"; // Your database name

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch users from the database
$sql = "SELECT * FROM users"; // Replace 'users' with your actual table name

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $users = array();
    while($row = $result->fetch_assoc()) {
        $user = array(
            'userId' => $row['Id'],
            'userName' => $row['Name']
        );
        array_push($users, $user);
    }
    // Return users data in JSON format
    echo json_encode($users);
} else {
    echo "0 results";
}
$conn->close();
?>
