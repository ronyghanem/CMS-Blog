<?php

session_start();

require_once 'classes/init.php';
require_once 'includes/otp.php';

$pdo = Database::getInstance();

$user = new User($pdo);

$error = '';
$success = '';


// Display resend error
if (isset($_SESSION['login_resend_error'])) {

    $error = $_SESSION['login_resend_error'];

    unset($_SESSION['login_resend_error']);
}


// Display resend success
if (isset($_SESSION['login_resend_success'])) {

    $success = $_SESSION['login_resend_success'];

    unset($_SESSION['login_resend_success']);
}


// Make sure user is waiting for login OTP
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


// Handle OTP submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $otp = trim($_POST['otp'] ?? '');


    // Validate OTP format
    if (!preg_match('/^\d{6}$/', $otp)) {

        $error = 'Please enter a valid 6-digit OTP.';

    } elseif ($otpUser['otp_purpose'] !== 'login') {

        $error = 'Invalid OTP purpose.';

    } elseif (!$otpUser['otp_expiry']) {

        $error = 'OTP is invalid or has expired.';

    } elseif (time() > strtotime($otpUser['otp_expiry'])) {

        $error = 'OTP has expired. Please log in again.';

    } elseif ($otpUser['otp_attempts'] >= 5) {

        $error = 'Too many incorrect attempts. Please log in again.';

    } elseif ($user->verifyOtp($userId, $otp)) {

        $pdo->beginTransaction();

        try {

            // Clear OTP information
            reset_otp_fields(
                $pdo,
                $userId
            );


            // Update last login
            $stmt = $pdo->prepare(
                "UPDATE users
                 SET last_login = NOW()
                 WHERE id = :id"
            );

            $stmt->execute([
                'id' => $userId
            ]);


            $pdo->commit();


            // Regenerate session ID
            session_regenerate_id(true);


            // Log user in
            $_SESSION['user_id'] = $otpUser['id'];
            $_SESSION['user_name'] = $otpUser['name'];
            $_SESSION['user_email'] = $otpUser['email'];


            // Remove OTP session data
            unset($_SESSION['pre_auth_user_id']);
            unset($_SESSION['test_login_otp']);
            unset($_SESSION['login_resend_error']);
            unset($_SESSION['login_resend_success']);


            // Dashboard
            header('Location: dashboard.php');
            exit;

        } catch (Exception $e) {

            if ($pdo->inTransaction()) {

                $pdo->rollBack();
            }

            $error = 'Something went wrong. Please try again.';
        }

    } else {

        // Incorrect OTP
        increment_otp_attempts(
            $pdo,
            $userId
        );


        $error = 'Incorrect OTP. Please try again.';


        // Refresh user
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

    <title>Login Verification - CMS Blog</title>


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

    </style>

</head>


<body>

    <div class="container">

        <div
            class="row justify-content-center align-items-center"
            style="min-height: 100vh;"
        >

            <div class="col-md-6 col-lg-4">

                <div class="card border-0 shadow-sm verification-card">

                    <div class="card-body p-4">


                        <!-- Header -->
                        <div class="text-center mb-4">

                            <div class="mb-3">

                                <div
                                    class="icon-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center"
                                >

                                    <i
                                        class="bi bi-shield-check text-primary"
                                        style="font-size: 2.5rem;"
                                    ></i>

                                </div>

                            </div>


                            <h1 class="fw-bold mb-1">
                                Login Verification
                            </h1>


                            <p class="text-muted mb-0">
                                Enter the code to complete your login
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
                        <?php if (isset($_SESSION['test_login_otp'])): ?>

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

                                    <?= htmlspecialchars(
                                        $_SESSION['test_login_otp']
                                    ) ?>

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


                            <!-- Verify -->
                            <div class="d-grid mb-3">

                                <button
                                    type="submit"
                                    class="btn btn-primary btn-lg"
                                >

                                    <i class="bi bi-check-circle me-1"></i>

                                    Verify Login

                                </button>

                            </div>

                        </form>


                        <!-- Resend -->
                        <div class="text-center mb-3">

                            <span class="text-muted">
                                Didn't receive a code?
                            </span>


                            <form
                                action="resend_login_otp.php"
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
                                href="login.php"
                                class="text-decoration-none"
                            >

                                <i class="bi bi-arrow-left me-1"></i>

                                Back to Login

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
