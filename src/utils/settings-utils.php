<?php
/**
 * Returns true if the user wants to receive notifications for new followers
 * @param $username the username
 * @param $dbh the database helper
 * @return bool true if the user wants to receive notifications for new followers
 */
function isNotificationFollowEnabled($username, DatabaseHelper $dbh){
    return $dbh->getSettings($username)[0]["alert-follow"];
}

/**
 * Returns true if the user wants to receive notifications for new followers of his animals
 * @param $username the username
 * @param $dbh the database helper
 * @return bool true if the user wants to receive notifications for new followers of his animals
 */
function isNotificationFollowAnimalEnabled($username, DatabaseHelper $dbh){
    return $dbh->getSettings($username)[0]["alert-follow-animal"];
}

/**
 * Returns true if the user wants to receive notifications for new likes on his posts
 * @param $username the username
 * @param $dbh the database helper
 * @return bool true if the user wants to receive notifications for new likes
 */
function isNotificationLikeEnabled($username, DatabaseHelper $dbh){
    return $dbh->getSettings($username)[0]["alert-likes"];
}

/**
 * Returns true if the user wants to receive notifications for new comments on his posts
 * @param $username the username
 * @param $dbh the database helper
 * @return bool true if the user wants to receive notifications for new comments
 */
function isNotificationCommentEnabled($username, DatabaseHelper $dbh){
    return $dbh->getSettings($username)[0]["alert-comments"];
}

/**
 * Returns true if the user wants to receive notifications for replies to his comments
 * @param $username the username
 * @param $dbh the database helper
 * @return bool true if the user wants to receive notifications for replies to his comments
 */
function isNotificationReplyEnabled($username, DatabaseHelper $dbh){
    return $dbh->getSettings($username)[0]["alert-comment-reply"];
}

/**
 * Returns true if the user wants to receive notifications for new posts of people he follows
 * @param $username the username
 * @param $dbh the database helper
 * @return bool true if the user wants to receive notifications for new posts of people he follows
 */
function isNotificationNewPostPersonEnabled($username, DatabaseHelper $dbh){
    return $dbh->getSettings($username)[0]["alert-new-post-person"];
}

/**
 * Returns true if the user wants to receive notifications for new posts of animals he follows
 * @param $username the username
 * @param $dbh the database helper
 * @return bool true if the user wants to receive notifications for new posts of animals he follows
 */
function isNotificationNewPostAnimalEnabled($username, DatabaseHelper $dbh){
    return $dbh->getSettings($username)[0]["alert-new-post-animal"];
}
