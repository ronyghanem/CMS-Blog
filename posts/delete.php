<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../classes/init.php';

$pdo = Database::getInstance();

$post = new Post($pdo);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int) $_GET['id'];

try {

    $post->delete($id);

    header('Location: index.php');
    exit;

} catch (PDOException $e) {

    die('Could not delete post.');
}