<?php
session_start();
require_once '../core/config.php';

// STEP 1: If no code → redirect to Google
if (!isset($_GET['code'])) {

    $authUrl = "https://accounts.google.com/o/oauth2/v2/auth?" . http_build_query([
        'client_id' => GOOGLE_CLIENT_ID,
        'redirect_uri' => GOOGLE_REDIRECT_URI,
        'response_type' => 'code',
        'scope' => 'openid email profile',
        'access_type' => 'online',
        'prompt' => 'select_account'
    ]);

    header("Location: $authUrl");
    exit;
}

// STEP 2: Exchange code for token
$tokenUrl = "https://oauth2.googleapis.com/token";

$postData = [
    'code' => $_GET['code'],
    'client_id' => GOOGLE_CLIENT_ID,
    'client_secret' => GOOGLE_CLIENT_SECRET,
    'redirect_uri' => GOOGLE_REDIRECT_URI,
    'grant_type' => 'authorization_code'
];

$options = [
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/x-www-form-urlencoded",
        'content' => http_build_query($postData)
    ]
];

$context = stream_context_create($options);
$response = file_get_contents($tokenUrl, false, $context);

$tokenData = json_decode($response, true);

if (!isset($tokenData['access_token'])) {
    die('Failed to get access token');
}

// STEP 3: Get user info
$userInfo = file_get_contents(
    "https://www.googleapis.com/oauth2/v2/userinfo?access_token=" . $tokenData['access_token']
);

$user = json_decode($userInfo, true);

$google_id = $user['id'];
$email = $user['email'];

require_once '../core/db.php';

// Check if user exists
$stmt = $conn->prepare("SELECT author_id FROM users WHERE google_id = ?");
$stmt->bind_param("s", $google_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {

    // NEW USER → generate author_id
    $author_id = 'author_' . bin2hex(random_bytes(8));

    $stmt = $conn->prepare("INSERT INTO users (google_id, email, author_id, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("sss", $google_id, $email, $author_id);
    $stmt->execute();

} else {

    // EXISTING USER → reuse author_id
    $row = $result->fetch_assoc();
    $author_id = $row['author_id'];
}

// Store in session
$_SESSION['user'] = [
    'google_id' => $google_id,
    'email' => $email,
    'author_id' => $author_id
];

// Redirect to main app
header("Location: studio.php");
exit;
?>
