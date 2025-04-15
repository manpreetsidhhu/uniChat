<!--
query for db:
    CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  profile_image VARCHAR(255),
  full_name VARCHAR(100),
  phone VARCHAR(20),
  email VARCHAR(100),
  github_link VARCHAR(255),
  linkedin_link VARCHAR(255),
  bio TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sender_id INT NOT NULL,
  receiver_id INT NOT NULL,
  message TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (sender_id) REFERENCES users(id),
  FOREIGN KEY (receiver_id) REFERENCES users(id)
); 


  -->
<?php
// Define database connection parameters
/*
$host = "localhost:3307";
$username = "root";
$password = "mysql@preet2549c1c9";
$dbname = "chat_website";
*/

//for phymyadmin use
$host = "localhost";
$username = "root";
$password = "";
$dbname = "chat_website";


// Establish a connection to the MySQL database
$conn = mysqli_connect($host, $username, $password, $dbname);

// Check if the connection was successful
if (!$conn) {
    // Terminate the script and display an error message if the connection fails
    die("Connection failed: " . mysqli_connect_error());
}
?>
