<?php
// Run this file once to setup the database
require_once 'config/database.php';

$db = new Database();
$conn = $db->getConnection();

if($conn) {
    echo "Database connection successful!<br>";
    
    if($db->setupDefaultUser()) {
        echo "Database tables created successfully!<br>";
        echo "Default admin user created:<br>";
        echo "Username: admin<br>";
        echo "Password: admin123<br>";
        echo "Email: admin@justiceflow.com<br><br>";
        echo "<a href='index.php'>Go to Homepage</a>";
    } else {
        echo "Error creating database tables.";
    }
} else {
    echo "Failed to connect to database. Please check your database configuration.";
}
?>