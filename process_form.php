<?php

// Connect to the local database
$db = new PDO('sqlite:/path/to/database.sqlite');

// Get the form data
$email = $_POST['email'];
$idno = $_POST['idno'];
$name = $_POST['name'];
$password = $_POST['password'];
$region = $_POST['region'];

// Prepare a SQL statement to insert the data into the database
$stmt = $db->prepare('INSERT INTO customers (email, idno, name, password, region) VALUES (:email, :idno, :name, :password, :region)');
$stmt->bindParam(':email', $email);
$stmt->bindParam(':idno', $idno);
$stmt->bindParam(':name', $name);
$stmt->bindParam(':password', $password);
$stmt->bindParam(':region', $region);

// Execute the SQL statement
$stmt->execute();

// Redirect the user to a confirmation page
header('Location: confirmation.php');
exit;

?>
