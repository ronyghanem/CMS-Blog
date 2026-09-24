<?php

session_start();

require_once 'classes/init.php';

$pdo = Database::getInstance();

$userManager = new User($pdo);

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);

    if (empty($email)) {

        $message = 'Please enter your email address.';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = 'Please enter a valid email address.';

    } else {

        try {

            $user = $userManager->findByEmail($email);

            if ($user) {

                $_SESSION['reset_user_id'] = $user['id'];

                header('Location: reset_password.php');
                exit;

            } else {

                $message = 'No account was found with this email address.';
            }

        } catch (PDOException $e) {

            $message = 'Something went wrong. Please try again.';
        }
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

    <title>Forgot Password - CMS Blog</title>

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

                <!-- Forgot Password Card -->
                <div class="card border-0 shadow-sm">

                    <div class="card-body p-4">

                        <!-- Icon / Title -->
                        <div class="text-center mb-4">

                            <div class="mb-3">

                                <i
                                    class="bi bi-key text-primary"
                                    style="font-size: 3rem;"
                                ></i>

                            </div>

                            <h1 class="fw-bold mb-1">
                                Forgot Password?
                            </h1>

                            <p class="text-muted mb-0">
                                Enter your email to reset your password.
                            </p>

                        </div>

                        <!-- Error Message -->
                        <?php if ($message): ?>

                            <div
                                class="alert alert-danger d-flex align-items-center"
                                role="alert"
                            >

                                <i class="bi bi-exclamation-circle me-2"></i>

                                <div>
                                    <?= htmlspecialchars($message) ?>
                                </div>

                            </div>

                        <?php endif; ?>

                        <!-- Form -->
                        <form method="POST" action="">

                            <div class="mb-4">

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
                                        required
                                    >

                                </div>

                            </div>

                            <!-- Submit -->
                            <div class="d-grid">

                                <button
                                    type="submit"
                                    class="btn btn-primary btn-lg"
                                >

                                    <i class="bi bi-arrow-right-circle me-1"></i>

                                    Continue

                                </button>

                            </div>

                        </form>

                        <!-- Back to Login -->
                        <div class="text-center mt-4">

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
