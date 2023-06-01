<?php

require_once("settings-utils.php");

/**
 * Parameters for the notification types are:
 * FOLLOW: follower
 * FOLLOW_ANIMAL: follower, animal
 * LIKE: user, post
 * COMMENT: user, post
 * REPLY_COMMENT: replier, comment, post
 * POST: user, post
 * MESSAGE: user
 */
enum NotificationType
{
    case FOLLOW; //Sent to the followed user when someone starts following him
    case FOLLOW_ANIMAL; //Sent to the animal's owner when someone starts following one of his animals
    case LIKE; //Sent to the post owner when someone likes his post
    case COMMENT; //Sent to the post owner when someone comments his post
    case REPLY_COMMENT; //Sent to the commenter when someone replies to his comment
    case POST; //Sent to the followers of a user when he posts something
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
        NotificationType::FOLLOW_ANIMAL => array("src" => getUserProfilePic(getNotificationParameter($notification, "follower"), $dbh), "alt" => "Foto profilo di " . getNotificationParameter($notification, "follower")),
        NotificationType::REPLY_COMMENT => array("src" => getUserProfilePic(getNotificationParameter($notification, "replier"), $dbh), "alt" => "Foto profilo di " . getNotificationParameter($notification, "replier")),
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
        NotificationType::FOLLOW_ANIMAL => getNotificationParameter($notification, "follower") . " ha iniziato a seguire " . getNotificationParameter($notification, "animal"),
        NotificationType::REPLY_COMMENT => getNotificationParameter($notification, "replier") . " ha risposto al tuo commento",
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
        NotificationType::FOLLOW_ANIMAL => getAnimalProfileHref(getNotificationParameter($notification, "animal")),
        NotificationType::REPLY_COMMENT => getPostHref(getNotificationParameter($notification, "post")),
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
        "FOLLOW_ANIMAL" => NotificationType::FOLLOW_ANIMAL,
        "REPLY_COMMENT" => NotificationType::REPLY_COMMENT,
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
    if (isNotificationFollowEnabled($followed, $dbh)) {
        $dbh->addNotification($followed, NotificationType::FOLLOW, array("follower" => $follower));
    }
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
    if (isNotificationCommentEnabled(getPost($post, $dbh)["username"], $dbh)) {
        $dbh->addNotification(getPost($post, $dbh)["username"], NotificationType::COMMENT, array("user" => $user, "post" => $post));
    }
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
    if (isNotificationLikeEnabled(getPost($post, $dbh)["username"], $dbh)) {
        $dbh->addNotification(getPost($post, $dbh)["username"], NotificationType::LIKE, array("user" => $user, "post" => $post));
    }
}

/**
 * Adds a notification for a new follower of an animal
 * @param $follower the follower
 * @param $animal the animal
 * @param $dbh the database helper
 * @return void
 */
function addFollowAnimalNotification($follower, $animal, DatabaseHelper $dbh)
{
    foreach ($dbh->getOwners($animal) as $owner) {
        if (isNotificationFollowAnimalEnabled($owner["username"], $dbh)) {
            $dbh->addNotification($owner["username"], NotificationType::FOLLOW_ANIMAL, array("follower" => $follower, "animal" => $animal));
        }
    }
}

/**
 * Adds a notification for a comment reply
 * @param $replier user id of the replier
 * @param $comment the comment id of the reply
 * @param $dbh the database helper
 * @return void
 */
function addReplyCommentNotification($replier, $comment, DatabaseHelper $dbh)
{
    $recipient = $dbh->getComment($comment)[0]["id_padre"];
    if (isset($recipient)) {
        if (isNotificationReplyEnabled($recipient, $dbh)){
            $dbh->addNotification($recipient, NotificationType::REPLY_COMMENT, array("replier" => $replier, "comment" => $comment, "post" => $dbh->getComment($comment)[0]["id_post"]));
        }
    } else {
        throw new Exception("Il commento non risponde a nessun altro commento");
    }
}
