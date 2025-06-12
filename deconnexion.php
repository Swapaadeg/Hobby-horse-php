<?php
include('function.php');
session_unset();
session_destroy();

// Supprimer le cookie
setcookie('auth_token', '', time() - 3600, '/');

header('location:index.php');