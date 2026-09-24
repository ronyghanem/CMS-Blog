<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require '../connection.php';

$sql = "SELECT
            posts.id,
            posts.title,
            posts.content,
            posts.date,
            posts.reading_time,
            categories.category_name,
            users.name AS author_name
        FROM posts
        INNER JOIN categories
            ON posts.category_id = categories.id
        INNER JOIN users
            ON posts.user_id = users.id
        ORDER BY posts.id DESC";

$stmt = $pdo->query($sql);

$posts = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>Posts - CMS Blog</title>

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


<!-- Navbar -->

<nav class="navbar navbar-dark bg-dark">

    <div class="container-fluid">

        <a
            class="navbar-brand fw-bold"
            href="../dashboard.php"
        >
            <i class="bi bi-journal-text"></i>
            CMS Blog
        </a>

        <div class="d-flex align-items-center">

            <span class="text-white me-3">
                <?= htmlspecialchars($_SESSION['user_name']) ?>
            </span>

            <a
                href="../logout.php"
                class="btn btn-outline-light btn-sm"
            >
                <i class="bi bi-box-arrow-right"></i>
                Logout
            </a>

        </div>

    </div>

</nav>


<div class="container-fluid">

    <div class="row">


        <!-- Sidebar -->

        <aside class="col-md-3 col-lg-2 bg-dark min-vh-100 p-3">

            <div class="nav flex-column nav-pills">

                <a
                    href="../dashboard.php"
                    class="nav-link text-white mb-2"
                >
                    <i class="bi bi-speedometer2"></i>
                    Dashboard
                </a>

                <a
                    href="../categories/index.php"
                    class="nav-link text-white mb-2"
                >
                    <i class="bi bi-tags"></i>
                    Categories
                </a>

                <a
                    href="index.php"
                    class="nav-link active mb-2"
                >
                    <i class="bi bi-file-text"></i>
                    Posts
                </a>

            </div>

        </aside>


        <!-- Main Content -->

        <main class="col-md-9 col-lg-10 p-4">


            <!-- Header -->

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h1 class="fw-bold mb-1">
                        Posts
                    </h1>

                    <p class="text-muted mb-0">
                        Manage your blog posts.
                    </p>

                </div>

                <a
                    href="create.php"
                    class="btn btn-primary"
                >
                    <i class="bi bi-plus-lg"></i>
                    Add Post
                </a>

            </div>


            <!-- Posts -->

            <div class="card border-0 shadow-sm">

                <div class="card-body">


                    <?php if (empty($posts)): ?>

                        <div class="text-center py-5">

                            <i class="bi bi-file-text fs-1 text-muted"></i>

                            <h4 class="mt-3">
                                No posts found
                            </h4>

                            <p class="text-muted">
                                Create your first blog post to get started.
                            </p>

                            <a
                                href="create.php"
                                class="btn btn-primary"
                            >
                                <i class="bi bi-plus-lg"></i>
                                Add Post
                            </a>

                        </div>


                    <?php else: ?>

                        <div class="table-responsive">

                            <table class="table table-hover align-middle mb-0">

                                <thead class="table-dark">

                                    <tr>

                                        <th>ID</th>

                                        <th>Title</th>

                                        <th>Category</th>

                                        <th>Author</th>

                                        <th>Date</th>

                                        <th>Reading Time</th>

                                        <th class="text-end">
                                            Actions
                                        </th>

                                    </tr>

                                </thead>


                                <tbody>

                                    <?php foreach ($posts as $post): ?>

                                        <tr>

                                            <td>
                                                <?= $post['id'] ?>
                                            </td>


                                            <td>

                                                <div class="fw-semibold">
                                                    <?= htmlspecialchars($post['title']) ?>
                                                </div>

                                                <small class="text-muted">

                                                    <?php
                                                    $preview = mb_substr(
                                                        $post['content'],
                                                        0,
                                                        60
                                                    );

                                                    echo htmlspecialchars($preview);

                                                    if (mb_strlen($post['content']) > 60) {
                                                        echo '...';
                                                    }
                                                    ?>

                                                </small>

                                            </td>


                                            <td>

                                                <span class="badge text-bg-primary">

                                                    <?= htmlspecialchars($post['category_name']) ?>

                                                </span>

                                            </td>


                                            <td>

                                                <i class="bi bi-person"></i>

                                                <?= htmlspecialchars($post['author_name']) ?>

                                            </td>


                                            <td>
                                                <?= htmlspecialchars($post['date']) ?>
                                            </td>


                                            <td>

                                                <i class="bi bi-clock"></i>

                                                <?= $post['reading_time'] ?> min

                                            </td>


                                            <td class="text-end">

                                                <a
                                                    href="edit.php?id=<?= $post['id'] ?>"
                                                    class="btn btn-sm btn-outline-primary"
                                                >
                                                    <i class="bi bi-pencil"></i>
                                                    Edit
                                                </a>


                                                <a
                                                    href="delete.php?id=<?= $post['id'] ?>"
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Are you sure you want to delete this post?');"
                                                >
                                                    <i class="bi bi-trash"></i>
                                                    Delete
                                                </a>

                                            </td>

                                        </tr>

                                    <?php endforeach; ?>

                                </tbody>

                            </table>

                        </div>

                    <?php endif; ?>

                </div>

            </div>

        </main>

    </div>

</div>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>

</body>

</html>