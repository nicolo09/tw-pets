<?php

/**
 * Parameters for the notification types are:
 * FOLLOW: follower
 * LIKE: user, post
 * COMMENT: user, post
 * POST: user, post
 * MESSAGE: user
 */
 enum NotificationType {
    case FOLLOW; //Sent to the followed user when someone starts following him
    case LIKE; //Sent to the post owner when someone likes his post
    case COMMENT; //Sent to the post owner when someone comments his post
    case POST; //Sent to the followers of a user when he posts something
    case MESSAGE; //Sent to the recipient when someone sends him a message
}

/**
 * Returns the thumbnail src for a notification
 * @param $notification the notification
 * @param $dbh the database helper
 * @return array src and alt of the thumbnail
 */
function getNotificationThumbnail($notification, DatabaseHelper $dbh)
{
    return match (getNotificationType($notification)) {
        NotificationType::FOLLOW => array("src" => getUserProfilePic(getNotificationParameter($notification, "follower"), $dbh), "alt" => "Foto profilo di " . getNotificationParameter($notification, "follower")),
        NotificationType::LIKE => array("src" => getUserProfilePic(getNotificationParameter($notification, "user"), $dbh), "alt" => "Foto profilo di " . getNotificationParameter($notification, "user")),
        NotificationType::COMMENT => array("src" => getUserProfilePic(getNotificationParameter($notification, "user"), $dbh), "alt" => "Foto profilo di " . getNotificationParameter($notification, "user")),
        default => array("src" => "", "alt" => "")
    };
}

/**
 * Returns the message of a notification
 * @param $notification the notification
 * @return string the message
 */
function getNotificationMessage($notification)
{
    return match (getNotificationType($notification)) {
        NotificationType::FOLLOW => getNotificationParameter($notification, "follower") . " ha iniziato a seguirti",
        NotificationType::LIKE => getNotificationParameter($notification, "user") . " ha messo mi piace al tuo post",
        NotificationType::COMMENT => getNotificationParameter($notification, "user") . " ha commentato il tuo post",
        default => ""
    };
}

/**
 * Returns the href of a notification
 * @param $notification the notification
 * @return string the ref
 */
function getNotificationRef($notification)
{
    return match (getNotificationType($notification)) {
        NotificationType::FOLLOW => getUserProfileHref(getNotificationParameter($notification, "follower")),
        NotificationType::LIKE => getPostHref(getNotificationParameter($notification, "post")),
        NotificationType::COMMENT => getPostHref(getNotificationParameter($notification, "post")),
        default => "#"
    };
}

/**
 * Returns the parameter of a notification
 * @param $notification the notification
 * @param $parameter the parameter
 * @return string the parameter
 */
function getNotificationParameter($notification, $parameter)
{
    $origin = json_decode($notification["origine"], true);
    if (isset($origin[$parameter])) {
        return $origin[$parameter];
    }
    return null;
}

/**
 * Returns the type of a notification
 * @param $notification the notification
 * @return NotificationType the type
 */
function getNotificationType($notification)
{
    return match ($notification["tipo"]) {
        "FOLLOW" => NotificationType::FOLLOW,
        "LIKE" => NotificationType::LIKE,
        "COMMENT" => NotificationType::COMMENT,
        "POST" => NotificationType::POST,
        "MESSAGE" => NotificationType::MESSAGE
    };
}

/**
 * Returns the date and time of a notification in the format "hh:mm:ss dd/mm/yyyy"
 * @param $notification the notification
 * @return string the date and time
 */
function getNotificationDateTime($notification)
{
    return date("H:i:s d/m/Y", strtotime($notification["timestamp"]));
}

/**
 * Returns true if the recipient for the specified notification is the current user
 * @param $notificationId the notification id
 * @param $user the user id
 * @param $dbh the database helper
 * @return bool true if the recipient for the specified notification is the current user
 */
function isNotificationForUser($notificationId, $user, $dbh)
{
    return $dbh->getNotification($notificationId)[0]["destinatario"] == $user;
}

/**
 * Adds a follow notification
 * @param $follower the follower
 * @param $followed the followed
 * @param $dbh the database helper
 * @return void
 */
function addFollowNotification($follower, $followed, DatabaseHelper $dbh)
{
    $dbh->addNotification($followed, NotificationType::FOLLOW, array("follower" => $follower));
}

/**
 * Adds a comment notification
 * @param $user the username
 * @param $post the post id
 * @param $dbh the database helper
 * @return void
 */
function addCommentNotification($user, $post, DatabaseHelper $dbh)
{
    $dbh->addNotification(getPost($post, $dbh)["username"], NotificationType::COMMENT, array("user" => $user, "post" => $post));
}

/**
 * Adds a like notification
 * @param $user the username
 * @param $post the post id
 * @param $dbh the database helper
 * @return void
 */
function addLikeNotification($user, $post, DatabaseHelper $dbh)
{
    $dbh->addNotification(getPost($post, $dbh)["username"], NotificationType::LIKE, array("user" => $user, "post" => $post));
}
