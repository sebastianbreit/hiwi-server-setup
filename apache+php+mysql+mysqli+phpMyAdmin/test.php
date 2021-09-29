<?php
//These are the defined authentication environment in the db service

// The MySQL service named in the docker-compose.yml.
$host = 'db-2';

// Database use name
$user = 'root';

//database user password
$pass = 'root';

// check the MySQL connection status
$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) {
    die("Connection 1 failed: " . $conn->connect_error);
} else {
    echo "1: Connected to MySQL server successfully!";
}

$host = 'db-2';

// Database use name
$user = 'test';

//database user password
$pass = 'test';

// check the MySQL connection status
$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) {
    die("Connection 2 failed: " . $conn->connect_error);
} else {
    echo "2: Connected to MySQL server successfully!";
}
?>
