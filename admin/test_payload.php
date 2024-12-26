<?php
// $json_str = file_get_contents('php://input');
// file_put_contents("test_payload.txt",$json_str);
$originalDateTime = '2024-04-01T09:13:51Z';

// Create a DateTime object from the original datetime string
$dateTime = new DateTime($originalDateTime);

// Convert the timezone to the desired timezone (if needed)
$dateTime->setTimezone(new DateTimeZone('Your_Timezone_Here'));

// Format the datetime as per your requirement (2024-04-01 10:34:29 format)
$formattedDateTime = $dateTime->format('Y-m-d H:i:s');

// Output the formatted datetime
echo $formattedDateTime;

?>