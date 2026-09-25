<?php

session_start();

require_once 'classes/init.php';
require_once 'includes/otp.php';

$pdo = Database::getInstance();

$user = new User($pdo);


// Make sure the user is waiting for login OTP
if (!isset($_SESSION['pre_auth_user_id'])) {

    header('Location: login.php');
    exit;
}


$userId = (int) $_SESSION['pre_auth_user_id'];


// Get user
$otpUser = $user->getOtpUser($userId);


if (!$otpUser) {

    unset($_SESSION['pre_auth_user_id']);

    header('Location: login.php');
    exit;
}


// Make sure this is a login OTP
if ($otpUser['otp_purpose'] !== 'login') {

    die('Invalid OTP request.');
}


// Check resend cooldown
$lastSentAt = $otpUser['otp_last_sent_at'];

if ($lastSentAt) {

    $secondsPassed = time() - strtotime($lastSentAt);

    $secondsRemaining = 60 - $secondsPassed;

    // Still inside the 60-second cooldown
    if ($secondsRemaining > 0) {

        // Convert remaining time into a readable format
        if ($secondsRemaining < 60) {

            $timeMessage = $secondsRemaining . ' seconds';

        } else {

            $minutes = floor($secondsRemaining / 60);
            $seconds = $secondsRemaining % 60;

            if ($seconds > 0) {

                $timeMessage =
                    $minutes . ' minute' .
                    ($minutes > 1 ? 's' : '') .
                    ' ' .
                    $seconds . ' second' .
                    ($seconds > 1 ? 's' : '');

            } else {

                $timeMessage =
                    $minutes . ' minute' .
                    ($minutes > 1 ? 's' : '');
            }
        }


        $_SESSION['login_resend_error'] =
            "Please wait {$timeMessage} before requesting a new OTP.";


        header('Location: login_otp.php');
        exit;
    }
}


// Generate new OTP
$otp = generate_numeric_otp();


// Hash OTP
$otpHash = hash_otp($otp);


// Set expiration
$expiry = otp_expiry(10);


// Store new OTP
$user->storeOtp(
    $userId,
    $otpHash,
    $expiry,
    'login'
);


// LOCALHOST TESTING ONLY
$_SESSION['test_login_otp'] = $otp;


// Success message
$_SESSION['login_resend_success'] =
    'A new OTP has been generated successfully.';


// Return to login verification page
header('Location: login_otp.php');
exit;
