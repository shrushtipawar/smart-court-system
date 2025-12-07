<?php
// Test script to verify DeepSeek API key
$apiKey = 'hYeKqE0RylTnImr86U4dGqr8sqwwG2KC';
$url = 'https://api.deepseek.com/v1/models';

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $apiKey,
    'Content-Type: application/json',
    'Accept: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

// Output the response
echo "HTTP Status: " . $httpCode . "\n";
echo "Response: " . $response . "\n";
if ($error) {
    echo "cURL Error: " . $error . "\n";
}
?>
