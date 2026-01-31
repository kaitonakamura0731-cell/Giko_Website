<?php
session_start();

function checkAuth()
{
    // Adjust the login path if necessary
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../login.php");
        exit;
    }
}
?>