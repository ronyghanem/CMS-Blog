<?php

session_start();

require_once 'classes/init.php';
require_once 'includes/otp.php';

$pdo = Database::getInstance();

$user = new User($pdo);

$error = '';
$success = '';


// Display resend error message
if (isset($_SESSION['resend_error'])) {

    $error = $_SESSION['resend_error'];

    unset($_SESSION['resend_error']);
}


// Display resend success message
if (isset($_SESSION['resend_success'])) {

    $success = $_SESSION['resend_success'];

    unset($_SESSION['resend_success']);
}


// Make sure a user is waiting for OTP verification
if (!isset($_SESSION['otp_user_id'])) {

    header('Location: register.php');
    exit;
}


$userId = (int) $_SESSION['otp_user_id'];


// Get the user
$otpUser = $user->getOtpUser($userId);

if (!$otpUser) {

    die('User not found.');
}


// Handle OTP submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $otp = trim($_POST['otp'] ?? '');


    // Check OTP format
    if (!preg_match('/^\d{6}$/', $otp)) {

        $error = 'Please enter a valid 6-digit OTP.';

    // Check OTP purpose
    } elseif ($otpUser['otp_purpose'] !== 'signup') {

        $error = 'Invalid OTP purpose.';

    // Check if OTP exists
    } elseif (!$otpUser['otp_expiry']) {

        $error = 'OTP is invalid or has expired.';

    // Check expiration
    } elseif (time() > strtotime($otpUser['otp_expiry'])) {

        $error = 'OTP has expired. Please request a new one.';

    // Check maximum attempts
    } elseif ($otpUser['otp_attempts'] >= 5) {

        $error = 'Too many incorrect attempts. Please request a new OTP.';

    // Verify OTP
    } elseif ($user->verifyOtp($userId, $otp)) {

        // Start database transaction
        $pdo->beginTransaction();

        try {

            // Activate account
            $user->markEmailVerified($userId);


            // Remove OTP information
            reset_otp_fields(
                $pdo,
                $userId
            );


            // Save changes
            $pdo->commit();


            // Regenerate session ID after successful verification
            session_regenerate_id(true);


            // Log the user in
            $_SESSION['user_id'] = $otpUser['id'];
            $_SESSION['user_name'] = $otpUser['name'];
            $_SESSION['user_email'] = $otpUser['email'];


            // Remove OTP session data
            unset($_SESSION['otp_user_id']);
            unset($_SESSION['test_otp']);
            unset($_SESSION['resend_error']);
            unset($_SESSION['resend_success']);


            // Redirect to dashboard
            header('Location: dashboard.php');
            exit;

        } catch (Exception $e) {

            // Roll back database changes
            if ($pdo->inTransaction()) {

                $pdo->rollBack();
            }

            $error = 'Something went wrong. Please try again.';
        }

    } else {

        // Wrong OTP
        increment_otp_attempts(
            $pdo,
            $userId
        );


        $error = 'Incorrect OTP. Please try again.';


        // Refresh user data
        $otpUser = $user->getOtpUser($userId);
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>Verify Email - CMS Blog</title>


    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <!-- Bootstrap Icons -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >


    <style>

        body {
            background: #f8f9fa;
        }

        .verification-card {
            border-radius: 16px;
        }

        .icon-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
        }

        .otp-input {
            letter-spacing: 8px;
            font-size: 1.5rem;
        }

        .resend-button {
            border: none;
            background: none;
        }

    </style>

</head>


<body>

    <div class="container">

        <div
            class="row justify-content-center align-items-center"
            style="min-height: 100vh;"
        >

            <div class="col-md-6 col-lg-4">

                <!-- Verification Card -->
                <div class="card border-0 shadow-sm verification-card">

                    <div class="card-body p-4">


                        <!-- Logo / Title -->
                        <div class="text-center mb-4">

                            <div class="mb-3">

                                <div
                                    class="icon-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center"
                                >

                                    <i
                                        class="bi bi-shield-lock text-primary"
                                        style="font-size: 2.5rem;"
                                    ></i>

                                </div>

                            </div>


                            <h1 class="fw-bold mb-1">
                                Verify Your Email
                            </h1>


                            <p class="text-muted mb-0">
                                Enter the 6-digit code to continue
                            </p>

                        </div>


                        <!-- Success Message -->
                        <?php if ($success): ?>

                            <div
                                class="alert alert-success d-flex align-items-center"
                                role="alert"
                            >

                                <i
                                    class="bi bi-check-circle me-2"
                                ></i>

                                <div>
                                    <?= htmlspecialchars($success) ?>
                                </div>

                            </div>

                        <?php endif; ?>


                        <!-- Error Message -->
                        <?php if ($error): ?>

                            <div
                                class="alert alert-danger d-flex align-items-center"
                                role="alert"
                            >

                                <i
                                    class="bi bi-exclamation-circle me-2"
                                ></i>

                                <div>
                                    <?= htmlspecialchars($error) ?>
                                </div>

                            </div>

                        <?php endif; ?>


                        <!-- Localhost OTP -->
                        <?php if (isset($_SESSION['test_otp'])): ?>

                            <div
                                class="alert alert-info text-center"
                                role="alert"
                            >

                                <div class="small text-muted mb-1">

                                    <i class="bi bi-code-square me-1"></i>

                                    Localhost Test OTP

                                </div>


                                <div
                                    class="fw-bold fs-3"
                                    style="letter-spacing: 8px;"
                                >

                                    <?= htmlspecialchars($_SESSION['test_otp']) ?>

                                </div>


                                <div class="small text-muted mt-1">

                                    For development testing only

                                </div>

                            </div>

                        <?php endif; ?>


                        <!-- OTP Form -->
                        <form method="POST">

                            <div class="mb-4">

                                <label
                                    for="otp"
                                    class="form-label fw-semibold"
                                >
                                    Verification Code
                                </label>


                                <div class="input-group input-group-lg">

                                    <span class="input-group-text">

                                        <i class="bi bi-key"></i>

                                    </span>


                                    <input
                                        type="text"
                                        id="otp"
                                        name="otp"
                                        class="form-control text-center fw-bold otp-input"
                                        maxlength="6"
                                        inputmode="numeric"
                                        autocomplete="one-time-code"
                                        placeholder="000000"
                                        pattern="[0-9]{6}"
                                        required
                                    >

                                </div>


                                <div class="form-text text-center mt-2">

                                    <i class="bi bi-clock me-1"></i>

                                    Your code is valid for 10 minutes.

                                </div>

                            </div>


                            <!-- Verify Button -->
                            <div class="d-grid mb-3">

                                <button
                                    type="submit"
                                    class="btn btn-primary btn-lg"
                                >

                                    <i class="bi bi-check-circle me-1"></i>

                                    Verify OTP

                                </button>

                            </div>

                        </form>


                        <!-- Resend -->
                        <div class="text-center mb-3">

                            <span class="text-muted">
                                Didn't receive a code?
                            </span>


                            <form
                                action="resend_otp.php"
                                method="POST"
                                class="d-inline"
                            >

                                <button
                                    type="submit"
                                    class="btn btn-link text-decoration-none fw-semibold p-0 ms-1"
                                >

                                    Resend OTP

                                </button>

                            </form>

                        </div>


                        <!-- Back -->
                        <div class="text-center">

                            <a
                                href="register.php"
                                class="text-decoration-none"
                            >

                                <i class="bi bi-arrow-left me-1"></i>

                                Back to Register

                            </a>

                        </div>

                    </div>

                </div>


                <!-- Footer -->
                <p class="text-center text-muted small mt-3">

                    <i class="bi bi-journal-text me-1"></i>

                    &copy; <?= date('Y') ?> CMS Blog

                </p>

            </div>

        </div>

    </div>


    <!-- Bootstrap JS -->
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    ></script>

</body>

</html>
