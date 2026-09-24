<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'classes/init.php';

$pdo = Database::getInstance();

$user = new User($pdo);
$category = new Category($pdo);
$post = new Post($pdo);

$totalUsers = $user->count();

$totalCategories = $category->count();

$totalPosts = $post->count();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>Dashboard - CMS Blog</title>

    <!-- Bootstrap 5 -->
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


<!--
|--------------------------------------------------------------------------
| Navbar
|--------------------------------------------------------------------------
-->

<nav class="navbar navbar-dark bg-dark">

    <div class="container-fluid">

        <a
            class="navbar-brand fw-bold"
            href="dashboard.php"
        >
            <i class="bi bi-journal-text"></i>
            CMS Blog
        </a>


        <div class="d-flex align-items-center">

            <span class="text-white me-3">
                <?= htmlspecialchars($_SESSION['user_name']) ?>
            </span>

            <a
                href="logout.php"
                class="btn btn-outline-light btn-sm"
            >
                <i class="bi bi-box-arrow-right"></i>
                Logout
            </a>

        </div>

    </div>

</nav>


<!--
|--------------------------------------------------------------------------
| Main layout
|--------------------------------------------------------------------------
-->

<div class="container-fluid">

    <div class="row">


        <!--
        |--------------------------------------------------------------------------
        | Sidebar
        |--------------------------------------------------------------------------
        -->

        <aside class="col-md-3 col-lg-2 bg-dark min-vh-100 p-3">

            <div class="nav flex-column nav-pills">

                <a
                    href="dashboard.php"
                    class="nav-link active mb-2"
                >
                    <i class="bi bi-speedometer2"></i>
                    Dashboard
                </a>


                <a
                    href="categories/index.php"
                    class="nav-link text-white mb-2"
                >
                    <i class="bi bi-tags"></i>
                    Categories
                </a>


                <a
                    href="posts/index.php"
                    class="nav-link text-white mb-2"
                >
                    <i class="bi bi-file-text"></i>
                    Posts
                </a>

            </div>

        </aside>


        <!--
        |--------------------------------------------------------------------------
        | Content
        |--------------------------------------------------------------------------
        -->

        <main class="col-md-9 col-lg-10 p-4">


            <!-- Page title -->

            <div class="mb-4">

                <h1 class="fw-bold">
                    Dashboard
                </h1>

                <p class="text-muted mb-0">
                    Welcome back to your CMS Blog.
                </p>

            </div>


            <!--
            |--------------------------------------------------------------------------
            | Welcome Card
            |--------------------------------------------------------------------------
            -->

            <div class="card border-0 shadow-sm mb-4">

                <div class="card-body p-4">

                    <h4 class="fw-bold">
                        Welcome,
                        <?= htmlspecialchars($_SESSION['user_name']) ?>!
                    </h4>

                    <p class="text-muted mb-1">
                        You are logged in as:
                    </p>

                    <p class="mb-0">
                        <i class="bi bi-envelope"></i>
                        <?= htmlspecialchars($_SESSION['user_email']) ?>
                    </p>

                </div>

            </div>


            <!--
            |--------------------------------------------------------------------------
            | Statistics
            |--------------------------------------------------------------------------
            -->

            <div class="row g-4">


                <!-- Users -->

                <div class="col-md-4">

                    <div class="card border-0 shadow-sm h-100">

                        <div class="card-body">

                            <div class="d-flex justify-content-between">

                                <div>

                                    <p class="text-muted mb-1">
                                        Users
                                    </p>

                                    <h2 class="fw-bold">
                                        <?= $totalUsers ?>
                                    </h2>

                                </div>

                                <div class="fs-1 text-primary">

                                    <i class="bi bi-people"></i>

                                </div>

                            </div>

                            <a
                                href="#"
                                class="text-decoration-none"
                            >
                                Registered users
                            </a>

                        </div>

                    </div>

                </div>


                <!-- Categories -->

                <div class="col-md-4">

                    <div class="card border-0 shadow-sm h-100">

                        <div class="card-body">

                            <div class="d-flex justify-content-between">

                                <div>

                                    <p class="text-muted mb-1">
                                        Categories
                                    </p>

                                    <h2 class="fw-bold">
                                        <?= $totalCategories ?>
                                    </h2>

                                </div>

                                <div class="fs-1 text-success">

                                    <i class="bi bi-tags"></i>

                                </div>

                            </div>

                            <a
                                href="categories/index.php"
                                class="text-decoration-none"
                            >
                                Manage categories
                            </a>

                        </div>

                    </div>

                </div>


                <!-- Posts -->

                <div class="col-md-4">

                    <div class="card border-0 shadow-sm h-100">

                        <div class="card-body">

                            <div class="d-flex justify-content-between">

                                <div>

                                    <p class="text-muted mb-1">
                                        Posts
                                    </p>

                                    <h2 class="fw-bold">
                                        <?= $totalPosts ?>
                                    </h2>

                                </div>

                                <div class="fs-1 text-danger">

                                    <i class="bi bi-file-text"></i>

                                </div>

                            </div>

                            <a
                                href="posts/index.php"
                                class="text-decoration-none"
                            >
                                Manage posts
                            </a>

                        </div>

                    </div>

                </div>


            </div>


            <!--
            |--------------------------------------------------------------------------
            | Quick Actions
            |--------------------------------------------------------------------------
            -->

            <div class="card border-0 shadow-sm mt-4">

                <div class="card-body p-4">

                    <h4 class="fw-bold mb-3">
                        Quick Actions
                    </h4>


                    <div class="d-flex gap-2 flex-wrap">

                        <a
                            href="posts/create.php"
                            class="btn btn-primary"
                        >
                            <i class="bi bi-plus-lg"></i>
                            New Post
                        </a>


                        <a
                            href="categories/create.php"
                            class="btn btn-success"
                        >
                            <i class="bi bi-plus-lg"></i>
                            New Category
                        </a>

                    </div>

                </div>

            </div>


        </main>

    </div>

</div>


<!-- Bootstrap JavaScript -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>

</body>

</html>