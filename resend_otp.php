<?php

session_start();

require_once 'classes/init.php';
require_once 'includes/otp.php';

$pdo = Database::getInstance();

$user = new User($pdo);

if (!isset($_SESSION['otp_user_id'])) {

    header('Location: register.php');
    exit;
}

$userId = (int) $_SESSION['otp_user_id'];

$otpUser = $user->getOtpUser($userId);

if (!$otpUser) {

    die('User not found.');
}

if ($otpUser['otp_purpose'] !== 'signup') {

    die('Invalid OTP request.');
}


// Check 60-second resend cooldown
if (!can_resend_otp(
    $otpUser['otp_last_sent_at'],
    60
)) {

    $secondsPassed = time() - strtotime(
        $otpUser['otp_last_sent_at']
    );

    $secondsRemaining = 60 - $secondsPassed;

    if ($secondsRemaining < 1) {
        $secondsRemaining = 1;
    }

    $_SESSION['resend_error'] =
        "Please wait {$secondsRemaining} seconds before requesting a new OTP.";

    header('Location: verify_otp.php');
    exit;
}


// Generate new OTP
$otp = generate_numeric_otp();


// Hash OTP
$otpHash = hash_otp($otp);


// OTP expires in 10 minutes
$expiry = otp_expiry(10);


// Store new OTP
$user->storeOtp(
    $userId,
    $otpHash,
    $expiry,
    'signup'
);


// LOCALHOST TESTING ONLY
$_SESSION['test_otp'] = $otp;


// Success message
$_SESSION['resend_success'] =
    'A new OTP has been generated successfully.';

header('Location: verify_otp.php');
exit;
