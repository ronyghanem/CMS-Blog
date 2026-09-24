<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../classes/init.php';

$pdo = Database::getInstance();

$category = new Category($pdo);

$message = '';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int) $_GET['id'];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $categoryName = trim($_POST['category_name']);

    if (empty($categoryName)) {

        $message = 'Please enter a category name.';

    } else {

        try {

            $category->update($id, $categoryName);

            header('Location: index.php');
            exit;

        } catch (PDOException $e) {

            $message = 'Could not update category. Please try again.';
        }
    }
}


$categoryData = $category->getById($id);


if (!$categoryData) {
    header('Location: index.php');
    exit;
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

    <title>Edit Category - CMS Blog</title>

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

                    <h1 class="fw-bold">
                        Edit Category
                    </h1>

                    <p class="text-muted">
                        Update the category information.
                    </p>


                    <?php if ($message): ?>

                        <div class="alert alert-danger">

                            <i class="bi bi-exclamation-circle"></i>

                            <?= htmlspecialchars($message) ?>

                        </div>

                    <?php endif; ?>


                    <form method="POST" action="">


                        <div class="mb-4">

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
                                value="<?= htmlspecialchars($categoryData['category_name']) ?>"
                                required
                            >

                        </div>


                        <div class="d-flex gap-2">

                            <button
                                type="submit"
                                class="btn btn-primary"
                            >
                                <i class="bi bi-check-lg"></i>
                                Update Category
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