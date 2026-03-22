<?php
session_start();

function getUser() {
    return $_SESSION['user'] ?? null;
}

function requireAuth() {
    if (!isset($_SESSION['user'])) {
        header("Location: login.php");
        exit;
    }
}

function logout() {
    $_SESSION = [];
    session_destroy();
    header("Location: login.php");
    exit;
}
?>
