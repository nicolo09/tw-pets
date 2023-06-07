<?php
require_once("bootstrap.php");
require_once("utils/notification-utils.php");

if (!login_check($dbh)) {
    header("Location: login.php");
    exit;
}

header("Content-Type: application/json");

if (isset($_GET["id"])) {
    $id = $_GET["id"];
    if ($id == "all") {
        //Delete all notifications
        $dbh->deleteAllNotifications(getUserName($dbh));
        echo json_encode(array("success" => true));
        exit;
    } else if (isNotificationForUser($id, getUserName($dbh), $dbh)) {
        //Delete a single specified notification
        $result = $dbh->deleteNotification($id);
        if ($result == true) {
            // Notification deleted successfully
            echo json_encode(array("success" => true));
            exit;
        } else {
            // Error deleting notification
            echo json_encode(array("success" => false, "error" => "Errore durante l'eliminazione della notifica"));
            exit;
        }
    } else {
        // Notification does not exist
        echo json_encode(array("success" => false, "error" => "Notifica non esistente"));
        exit;
    }
}
