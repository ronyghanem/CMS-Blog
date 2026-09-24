<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require '../connection.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $categoryName = trim($_POST['category_name']);

    if (empty($categoryName)) {

        $message = 'Please enter a category name.';

    } else {

        try {

            $sql = "INSERT INTO categories (category_name)
                    VALUES (:category_name)";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ':category_name' => $categoryName
            ]);

            header('Location: index.php');
            exit;

        } catch (PDOException $e) {

            $message = 'Could not create category. Please try again.';
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

    <title>Add Category - CMS Blog</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

</head>

<body class="bg-light">


<nav class="navbar navbar-dark bg-dark">

    <div class="container-fluid">

        <a
            class="navbar-brand fw-bold"
            href="../dashboard.php"
        >
            <i class="bi bi-journal-text"></i>
            CMS Blog
        </a>

        <a
            href="../logout.php"
            class="btn btn-outline-light btn-sm"
        >
            <i class="bi bi-box-arrow-right"></i>
            Logout
        </a>

    </div>

</nav>


<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-md-7 col-lg-6">

            <div class="card border-0 shadow-sm">

                <div class="card-body p-4">

                    <div class="mb-4">

                        <h1 class="fw-bold">
                            Add Category
                        </h1>

                        <p class="text-muted">
                            Create a new blog category.
                        </p>

                    </div>


                    <?php if ($message): ?>

                        <div class="alert alert-danger">

                            <i class="bi bi-exclamation-circle"></i>

                            <?= htmlspecialchars($message) ?>

                        </div>

                    <?php endif; ?>


                    <form method="POST" action="">


                        <div class="mb-3">

                            <label
                                for="category_name"
                                class="form-label fw-semibold"
                            >
                                Category Name
                            </label>

                            <input
                                type="text"
                                id="category_name"
                                name="category_name"
                                class="form-control"
                                placeholder="e.g. Programming"
                                required
                            >

                        </div>


                        <div class="d-flex gap-2">

                            <button
                                type="submit"
                                class="btn btn-primary"
                            >
                                <i class="bi bi-check-lg"></i>
                                Create Category
                            </button>


                            <a
                                href="index.php"
                                class="btn btn-outline-secondary"
                            >
                                Cancel
                            </a>

                        </div>


                    </form>

                </div>

            </div>

        </div>

    </div>

</div>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>

</body>

</html>