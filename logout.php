<?php
session_start();
require_once 'includes/UserAuth.php';

$userAuth = new UserAuth(null);
$userAuth->logout();

header("Location: login.php");
exit();
?>
