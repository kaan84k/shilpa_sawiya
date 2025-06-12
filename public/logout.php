<?php
session_start();
use App\Models\UserAuth;

$userAuth = new UserAuth(null);
$userAuth->logout();
header("Location: login.php");
exit();
?>
