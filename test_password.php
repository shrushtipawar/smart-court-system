<?php
$passwords = [
    '',           // empty
    'root',       // most common
    'password',
    'admin',
    '123456',
    '1234',
    'mysql',
    '123'
];

foreach ($passwords as $pwd) {
    try {
        $conn = new PDO("mysql:host=localhost", "root", $pwd);
        echo "<div style='color:green; padding:10px; border:1px solid green; margin:5px;'>
                ✓ Success! Password is: <strong>'$pwd'</strong>
              </div>";
        break;
    } catch (PDOException $e) {
        echo "<div style='color:red; padding:10px; border:1px solid #ccc; margin:5px;'>
                ✗ Failed: '$pwd'
              </div>";
    }
}
?>