<?php
// backend.php

$host = 'database'; // Hostname of the MySQL container in Docker
$user = 'root'; // MySQL root user
$password = 'pass123'; // MySQL root password
$dbname = 'facebook'; // Correct database name

// Enable error reporting for debugging (Disable in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    // Create connection
    $conn = new mysqli($host, $user, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Handle POST request
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);

        // Validate inputs
        if (!empty($name) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // Insert data
            $stmt = $conn->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
            $stmt->bind_param("ss", $name, $email);

            if ($stmt->execute()) {
                echo "Data inserted successfully!";
            } else {
                echo "Error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Invalid input. Please ensure the name is not empty and the email is valid.";
        }
    }

    // Fetch all data
    $result = $conn->query("SELECT * FROM users");

    // Display data in a table
    if ($result && $result->num_rows > 0) {
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Name</th><th>Email</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>" . htmlspecialchars($row['id']) . "</td><td>" . htmlspecialchars($row['name']) . "</td><td>" . htmlspecialchars($row['email']) . "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "No data found.";
    }

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
} finally {
    // Close connection
    if (isset($conn)) {
        $conn->close();
    }
}
?>
