<?php

session_start();
if (!isset($_SESSION['username'])) {
	http_response_code(401);
	exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$jsonString = file_get_contents("php://input");
    $jsonData = json_decode($jsonString);

    // Check if decoding was successful and if the JSON is valid
    if ($jsonData === null && json_last_error() !== JSON_ERROR_NONE) {
        echo('invalid data');
		http_response_code(400);
		exit;
    }

	$content = $jsonData->content;
	$role = $jsonData->role;

	$sanitizedContent = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
	$sanitizedRole = htmlspecialchars($role, ENT_QUOTES, 'UTF-8');
	
	if(empty($sanitizedContent) || empty($sanitizedRole)) {
		echo('invalid data');
		http_response_code(400);
		exit;
	}

	$sanitizedJsonString = "{\"role\":\"$sanitizedRole\",\"content\":\"$sanitizedContent\"}";
	
	$uniqid = userpost . phptime() . uniqid();
	file_put_contents("feedback/$uniqid.json", $sanitizedJsonString);
	echo $sanitizedJsonString;
}