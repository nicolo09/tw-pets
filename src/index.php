<?php
require_once("bootstrap.php");

if (!login_check($dbh)) {
    header("Location: login.php");
    exit;
} else {
    header("Location: home.php");
    exit;
}
?>
