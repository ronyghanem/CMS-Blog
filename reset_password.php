<?php

session_start();

require 'connection.php';

$message = '';

if (!isset($_SESSION['reset_user_id'])) {

    header('Location: forgot_password.php');
    exit;
}

$userId = $_SESSION['reset_user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if (empty($password) || empty($confirmPassword)) {

        $message = 'Please fill in both password fields.';

    } elseif (strlen($password) < 6) {

        $message = 'Password must be at least 6 characters.';

    } elseif ($password !== $confirmPassword) {

        $message = 'Passwords do not match.';

    } else {

        try {

            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $sql = "UPDATE users
                    SET password = :password
                    WHERE id = :id";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ':password' => $hashedPassword,
                ':id' => $userId
            ]);

            unset($_SESSION['reset_user_id']);

            header('Location: login.php');
            exit;

        } catch (PDOException $e) {

            $message = 'Could not reset password. Please try again.';
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

    <title>Reset Password - CMS Blog</title>

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

                <!-- Reset Password Card -->
                <div class="card border-0 shadow-sm">

                    <div class="card-body p-4">

                        <!-- Icon / Title -->
                        <div class="text-center mb-4">

                            <div class="mb-3">

                                <i
                                    class="bi bi-shield-lock text-primary"
                                    style="font-size: 3rem;"
                                ></i>

                            </div>

                            <h1 class="fw-bold mb-1">
                                Reset Password
                            </h1>

                            <p class="text-muted mb-0">
                                Create a new password for your account.
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

                        <!-- Reset Form -->
                        <form method="POST" action="">

                            <!-- New Password -->
                            <div class="mb-3">

                                <label
                                    for="password"
                                    class="form-label fw-semibold"
                                >
                                    New Password
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
                                        placeholder="Enter new password"
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
                                        placeholder="Confirm new password"
                                        required
                                    >

                                </div>

                            </div>

                            <!-- Reset Button -->
                            <div class="d-grid">

                                <button
                                    type="submit"
                                    class="btn btn-primary btn-lg"
                                >

                                    <i class="bi bi-check-circle me-1"></i>

                                    Reset Password

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
