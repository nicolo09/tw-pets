<?php
require_once("bootstrap.php");
require_once("utils/notification-utils.php");

if (!login_check($dbh)) {
    header("Location: login.php");
    exit;
}

if (isset($_GET["number"]) && isset($_GET["offset"])) {
    $notifications = $dbh->getNotifications(getUserName($dbh), $_GET["number"], $_GET["offset"]);
    foreach ($notifications as $notification) {
        $thumbnail = getNotificationThumbnail($notification, $dbh);
        require("template/notification.php");
    }
    exit;
}
else {
    exit;
}
