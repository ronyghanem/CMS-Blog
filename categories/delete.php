<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../classes/init.php';

$pdo = Database::getInstance();

$category = new Category($pdo);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int) $_GET['id'];

try {

    $category->delete($id);

    header('Location: index.php');
    exit;

} catch (PDOException $e) {

    die('Could not delete category.');
}