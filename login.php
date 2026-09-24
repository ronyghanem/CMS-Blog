<?php

session_start();

require 'connection.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {

        $message = 'Please enter your email and password.';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = 'Please enter a valid email address.';

    } else {

        try {

            $sql = "SELECT id, name, email, password
                    FROM users
                    WHERE email = :email";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ':email' => $email
            ]);

            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];

                header('Location: dashboard.php');
                exit;

            } else {

                $message = 'Invalid email or password.';
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

    <title>Login - CMS Blog</title>

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

            <div class="col-md-6 col-lg-4">

                <!-- Login Card -->
                <div class="card border-0 shadow-sm">

                    <div class="card-body p-4">

                        <!-- Logo / Title -->
                        <div class="text-center mb-4">

                            <div class="mb-3">

                                <i
                                    class="bi bi-journal-text text-primary"
                                    style="font-size: 3rem;"
                                ></i>

                            </div>

                            <h1 class="fw-bold mb-1">
                                CMS Blog
                            </h1>

                            <p class="text-muted mb-0">
                                Sign in to your account
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

                        <!-- Login Form -->
                        <form method="POST" action="">

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
                                        placeholder="Enter your password"
                                        required
                                    >

                                </div>

                            </div>

                            <!-- Forgot Password -->
                            <div class="text-end mb-4">

                                <a
                                    href="forgot_password.php"
                                    class="text-decoration-none"
                                >
                                    Forgot Password?
                                </a>

                            </div>

                            <!-- Login Button -->
                            <div class="d-grid">

                                <button
                                    type="submit"
                                    class="btn btn-primary btn-lg"
                                >

                                    <i class="bi bi-box-arrow-in-right me-1"></i>

                                    Login

                                </button>

                            </div>

                        </form>

                        <!-- Register Link -->
                        <div class="text-center mt-4">

                            <p class="text-muted mb-0">

                                Don't have an account?

                                <a
                                    href="register.php"
                                    class="text-decoration-none fw-semibold"
                                >
                                    Create one
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
