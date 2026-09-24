<?php

require 'connection.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($name) || empty($email) || empty($password)) {

        $message = 'Please fill in all fields.';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = 'Please enter a valid email address.';

    } elseif (strlen($password) < 6) {

        $message = 'Password must be at least 6 characters.';

    } else {

        try {

            $sql = "SELECT id
                    FROM users
                    WHERE email = :email";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ':email' => $email
            ]);

            if ($stmt->fetch()) {

                $message = 'This email is already registered.';

            } else {

                $hashedPassword = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );

                $sql = "INSERT INTO users (name, email, password)
                        VALUES (:name, :email, :password)";

                $stmt = $pdo->prepare($sql);

                $stmt->execute([
                    ':name' => $name,
                    ':email' => $email,
                    ':password' => $hashedPassword
                ]);

                $message = 'Registration successful!';
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

                        <!-- Message -->
                        <?php if ($message): ?>

                            <div
                                class="alert <?= $message === 'Registration successful!' ? 'alert-success' : 'alert-danger' ?> d-flex align-items-center"
                                role="alert"
                            >

                                <i
                                    class="bi <?= $message === 'Registration successful!' ? 'bi-check-circle' : 'bi-exclamation-circle' ?> me-2"
                                ></i>

                                <div>
                                    <?= htmlspecialchars($message) ?>
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
                                        required
                                    >

                                </div>

                            </div>

                            <!-- Password -->
                            <div class="mb-4">

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
                                        required
                                    >

                                </div>

                                <div class="form-text">
                                    Password must contain at least 6 characters.
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
