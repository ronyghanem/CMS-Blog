<?php

session_start();

require_once 'classes/init.php';
require_once 'includes/otp.php';

$pdo = Database::getInstance();

$user = new User($pdo);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Validate required fields
    if (
        empty($name) ||
        empty($email) ||
        empty($password) ||
        empty($confirmPassword)
    ) {

        $error = 'All fields are required.';

    // Validate email
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = 'Please enter a valid email address.';

    // Validate password length
    } elseif (strlen($password) < 6) {

        $error = 'Password must contain at least 6 characters.';

    // Validate password confirmation
    } elseif ($password !== $confirmPassword) {

        $error = 'Passwords do not match.';

    // Check if email already exists
    } elseif ($user->emailExists($email)) {

        $error = 'An account with this email already exists.';

    } else {

        // Create the user
        $user->create(
            $name,
            $email,
            $password
        );

        // Get the newly created user
        $newUser = $user->findByEmail($email);

        $userId = (int) $newUser['id'];

        // Generate OTP
        $otp = generate_numeric_otp();

        // Hash OTP
        $otpHash = hash_otp($otp);

        // OTP expires in 10 minutes
        $expiry = otp_expiry(10);

        // Store OTP
        $user->storeOtp(
            $userId,
            $otpHash,
            $expiry,
            'signup'
        );

        // Mark user as pending
        $pdo->prepare(
            "UPDATE users
             SET status = 'pending',
                 email_verified = 0
             WHERE id = :id"
        )->execute([
            'id' => $userId
        ]);

        // Store user ID for OTP verification
        $_SESSION['otp_user_id'] = $userId;

        // LOCALHOST TESTING ONLY
        $_SESSION['test_otp'] = $otp;

        // Redirect to OTP verification
        header('Location: verify_otp.php');
        exit;
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

    <title>Register - CMS Blog</title>

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

</head>

<body class="bg-light">

    <div class="container">

        <div
            class="row justify-content-center align-items-center"
            style="min-height: 100vh;"
        >

            <div class="col-md-7 col-lg-5">

                <!-- Register Card -->
                <div class="card border-0 shadow-sm">

                    <div class="card-body p-4">

                        <!-- Logo / Title -->
                        <div class="text-center mb-4">

                            <div class="mb-3">

                                <i
                                    class="bi bi-person-plus text-primary"
                                    style="font-size: 3rem;"
                                ></i>

                            </div>

                            <h1 class="fw-bold mb-1">
                                Create Account
                            </h1>

                            <p class="text-muted mb-0">
                                Create your CMS Blog account
                            </p>

                        </div>


                        <!-- Error Message -->
                        <?php if ($error): ?>

                            <div
                                class="alert alert-danger d-flex align-items-center"
                                role="alert"
                            >

                                <i class="bi bi-exclamation-circle me-2"></i>

                                <div>
                                    <?= htmlspecialchars($error) ?>
                                </div>

                            </div>

                        <?php endif; ?>


                        <!-- Registration Form -->
                        <form method="POST" action="">

                            <!-- Name -->
                            <div class="mb-3">

                                <label
                                    for="name"
                                    class="form-label fw-semibold"
                                >
                                    Full Name
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text">
                                        <i class="bi bi-person"></i>
                                    </span>

                                    <input
                                        type="text"
                                        id="name"
                                        name="name"
                                        class="form-control"
                                        placeholder="Enter your name"
                                        value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                                        required
                                    >

                                </div>

                            </div>


                            <!-- Email -->
                            <div class="mb-3">

                                <label
                                    for="email"
                                    class="form-label fw-semibold"
                                >
                                    Email Address
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text">
                                        <i class="bi bi-envelope"></i>
                                    </span>

                                    <input
                                        type="email"
                                        id="email"
                                        name="email"
                                        class="form-control"
                                        placeholder="you@example.com"
                                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                        required
                                    >

                                </div>

                            </div>


                            <!-- Password -->
                            <div class="mb-3">

                                <label
                                    for="password"
                                    class="form-label fw-semibold"
                                >
                                    Password
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text">
                                        <i class="bi bi-lock"></i>
                                    </span>

                                    <input
                                        type="password"
                                        id="password"
                                        name="password"
                                        class="form-control"
                                        placeholder="Minimum 6 characters"
                                        minlength="6"
                                        required
                                    >

                                </div>

                                <div class="form-text">
                                    Password must contain at least 6 characters.
                                </div>

                            </div>


                            <!-- Confirm Password -->
                            <div class="mb-4">

                                <label
                                    for="confirm_password"
                                    class="form-label fw-semibold"
                                >
                                    Confirm Password
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text">
                                        <i class="bi bi-lock-fill"></i>
                                    </span>

                                    <input
                                        type="password"
                                        id="confirm_password"
                                        name="confirm_password"
                                        class="form-control"
                                        placeholder="Re-enter your password"
                                        minlength="6"
                                        required
                                    >

                                </div>

                            </div>


                            <!-- Register Button -->
                            <div class="d-grid">

                                <button
                                    type="submit"
                                    class="btn btn-primary btn-lg"
                                >

                                    <i class="bi bi-person-plus me-1"></i>

                                    Create Account

                                </button>

                            </div>

                        </form>


                        <!-- Login Link -->
                        <div class="text-center mt-4">

                            <p class="text-muted mb-0">

                                Already have an account?

                                <a
                                    href="login.php"
                                    class="text-decoration-none fw-semibold"
                                >
                                    Login
                                </a>

                            </p>

                        </div>

                    </div>

                </div>


                <!-- Footer -->
                <p class="text-center text-muted small mt-3">

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
