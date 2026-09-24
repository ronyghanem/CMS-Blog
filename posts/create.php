<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../classes/init.php';

$pdo = Database::getInstance();

$category = new Category($pdo);
$user = new User($pdo);
$post = new Post($pdo);

$message = '';


/*
|--------------------------------------------------------------------------
| Get categories
|--------------------------------------------------------------------------
*/

$categories = $category->getAll();


/*
|--------------------------------------------------------------------------
| Get users
|--------------------------------------------------------------------------
*/

$users = $user->getAll();


/*
|--------------------------------------------------------------------------
| Handle form
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = trim($_POST['title']);
    $categoryId = $_POST['category_id'];
    $userId = $_POST['user_id'];
    $content = trim($_POST['content']);
    $date = $_POST['date'];
    $readingTime = $_POST['reading_time'];


    if (
        empty($title) ||
        empty($categoryId) ||
        empty($userId) ||
        empty($content) ||
        empty($date) ||
        empty($readingTime)
    ) {

        $message = 'Please fill in all fields.';

    } elseif (!is_numeric($categoryId)) {

        $message = 'Invalid category.';

    } elseif (!is_numeric($userId)) {

        $message = 'Invalid author.';

    } elseif (!is_numeric($readingTime) || $readingTime <= 0) {

        $message = 'Reading time must be a positive number.';

    } else {

        try {

            $post->create(
                $title,
                $categoryId,
                $userId,
                $content,
                $date,
                $readingTime
            );

            header('Location: index.php');
            exit;

        } catch (PDOException $e) {

            $message = 'Could not create post. Please try again.';
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

    <title>Add Post - CMS Blog</title>

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

        <div class="col-lg-9">

            <div class="card border-0 shadow-sm">

                <div class="card-body p-4 p-md-5">

                    <h1 class="fw-bold">
                        Create New Post
                    </h1>

                    <p class="text-muted mb-4">
                        Write and publish a new blog post.
                    </p>


                    <?php if ($message): ?>

                        <div class="alert alert-danger">

                            <i class="bi bi-exclamation-circle"></i>

                            <?= htmlspecialchars($message) ?>

                        </div>

                    <?php endif; ?>


                    <form method="POST" action="">


                        <!-- Title -->

                        <div class="mb-3">

                            <label
                                for="title"
                                class="form-label fw-semibold"
                            >
                                Title
                            </label>

                            <input
                                type="text"
                                id="title"
                                name="title"
                                class="form-control"
                                placeholder="Enter post title"
                                required
                            >

                        </div>


                        <div class="row">


                            <!-- Category -->

                            <div class="col-md-6 mb-3">

                                <label
                                    for="category_id"
                                    class="form-label fw-semibold"
                                >
                                    Category
                                </label>

                                <select
                                    id="category_id"
                                    name="category_id"
                                    class="form-select"
                                    required
                                >

                                    <option value="">
                                        Select a category
                                    </option>

                                    <?php foreach ($categories as $categoryItem): ?>

                                        <option
                                            value="<?= $categoryItem['id'] ?>"
                                        >
                                            <?= htmlspecialchars($categoryItem['category_name']) ?>
                                        </option>

                                    <?php endforeach; ?>

                                </select>

                            </div>


                            <!-- Author -->

                            <div class="col-md-6 mb-3">

                                <label
                                    for="user_id"
                                    class="form-label fw-semibold"
                                >
                                    Author
                                </label>

                                <select
                                    id="user_id"
                                    name="user_id"
                                    class="form-select"
                                    required
                                >

                                    <option value="">
                                        Select an author
                                    </option>

                                    <?php foreach ($users as $userItem): ?>

                                        <option
                                            value="<?= $userItem['id'] ?>"
                                        >
                                            <?= htmlspecialchars($userItem['name']) ?>
                                        </option>

                                    <?php endforeach; ?>

                                </select>

                            </div>

                        </div>


                        <!-- Content -->

                        <div class="mb-3">

                            <label
                                for="content"
                                class="form-label fw-semibold"
                            >
                                Content
                            </label>

                            <textarea
                                id="content"
                                name="content"
                                class="form-control"
                                rows="10"
                                placeholder="Write your post content..."
                                required
                            ></textarea>

                        </div>


                        <div class="row">


                            <!-- Date -->

                            <div class="col-md-6 mb-3">

                                <label
                                    for="date"
                                    class="form-label fw-semibold"
                                >
                                    Date
                                </label>

                                <input
                                    type="date"
                                    id="date"
                                    name="date"
                                    class="form-control"
                                    required
                                >

                            </div>


                            <!-- Reading Time -->

                            <div class="col-md-6 mb-3">

                                <label
                                    for="reading_time"
                                    class="form-label fw-semibold"
                                >
                                    Reading Time (minutes)
                                </label>

                                <input
                                    type="number"
                                    id="reading_time"
                                    name="reading_time"
                                    class="form-control"
                                    min="1"
                                    placeholder="5"
                                    required
                                >

                            </div>

                        </div>


                        <hr class="my-4">


                        <div class="d-flex gap-2">

                            <button
                                type="submit"
                                class="btn btn-primary"
                            >
                                <i class="bi bi-check-lg"></i>
                                Create Post
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