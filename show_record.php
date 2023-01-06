<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username = "root";
$password = "Housing@789";
$dbname = "hfa_1sept";


$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}


 
$sql="SELECT status, count('status') as count FROM `survey_family_data` group by status";

$result=$conn->query( $sql );

while( $rs=$result->fetch_object() ) {
    echo '<div>Status: ' . $rs->status.', Total: ' . $rs->count . '</div>';
}

$conn->close();
$conn=null;
?>
