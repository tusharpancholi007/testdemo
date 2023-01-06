<html>
<body>
<a href="show_record.php">Show Current Status of Record</a>

<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define("department", "NIC");
define("servicecode", "TestCred");
define("key", "o2etc739ut");
$servername = "localhost";
$username = "root";
$password = "Housing@789";
$dbname = "hfa_1sept";
$url = 'http://10.88.235.138/PPPapi/api/Account/GetFamilyIncome';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}
$notFound = $inserted = 0; 
//$sql = "SELECT family_id FROM `survey_family_data` WHERE `family_id` = '6pmc5757'";
//$sql = "SELECT family_id FROM survey_family_data WHERE status=0 and family_id != 'null' limit 100";
$sql = "SELECT DISTINCT(family_id) FROM survey_family_data WHERE status= 0 and family_id != 'null' limit 50000";
$result = $conn->query($sql);
 

if ($result->num_rows) {
	foreach($result as $family){
		//print_r($family);EXIT;
		$familyId= $family['family_id'];
		$send = '{
			"DeptCode": "department",
		   "ServiceCode": "servicecode",
		   "DeptKey": "key",
			"FID": "'.$familyId.'"
	     }';
	$curl = curl_init();
	  curl_setopt_array($curl, array(
		CURLOPT_URL => $url,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS => $send,
		CURLOPT_HTTPHEADER => array(
			'Content-Type: application/json'
		),
	));

	$response = curl_exec($curl);
	curl_close($curl);
	$data = json_decode($response, true);
	//echo "<pre>"; print_r($data); exit;

	if($data['status'] == 'Successfull')
	{				
			 $api_familyIncome = $data['result']['familyIncome'];
			 $api_isIncomeVerified = $data['result']['isIncomeVerified'];
			 $api_response = $data['result']['response'];
			 $api_status = $data['status'];
			 $api_message = $data['message'];
			 $status = '1';
			$member_id = $data['result']['memberList'][0]['memberID'];
			
		
				$query2= "INSERT INTO log_ppp (family_id, member_id, api_familyIncome, api_isIncomeVerified, api_response, api_status, api_message )VALUES ('".$familyId."','".$member_id."','".$api_familyIncome."','".$api_isIncomeVerified."','".$api_response."','".$api_status."','".$api_message."')";
					$result2 = $conn->query($query2);
					
				//1 - API data recieve (status) 
				$sql = "UPDATE survey_family_data set status=1 WHERE family_id='$familyId'";
					$result2 = $conn->query($sql);
					$inserted++;
			

		} else {
			//2 - API data not recieve (status)	
			$sql = "UPDATE survey_family_data set status=2 WHERE family_id='$familyId'";
			$result2 = $conn->query($sql);
			$notFound++;
		}
	
	}
	}
	$conn->close();
	echo 'Inserted - '.$inserted.'<br>'.'Not match - '.$notFound;
	
	
	
?>

</body>
</html>