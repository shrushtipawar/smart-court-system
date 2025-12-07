<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

// Get the raw POST data
$input = json_decode(file_get_contents('php://input'), true);

if (empty($input['message'])) {
    http_response_code(400);
    echo json_encode(['error' => 'No message provided']);
    exit();
}

$apiKey = 'hYeKqE0RylTnImr86U4dGqr8sqwwG2KC';
$url = 'https://api.deepseek.com/v1/chat/completions';

$data = [
    'model' => 'deepseek-chat',
    'messages' => [
        ['role' => 'system', 'content' => 'You are a helpful legal assistant.'],
        ['role' => 'user', 'content' => $input['message']]
    ],
    'temperature' => 0.7,
    'max_tokens' => 500
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $apiKey,
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    http_response_code(500);
    echo json_encode(['error' => 'cURL Error: ' . $error]);
    exit();
}

http_response_code($httpCode);
echo $response;
