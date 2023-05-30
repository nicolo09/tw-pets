<?php

$result = $dbh->getPostsForUser(getUserName($dbh),10,0);

foreach ($result as $post) {
    $templateParams["id"] = $post["id_post"];
    $templateParams["immagine"] = IMG_DIR . $post["immagine"];
    $templateParams["alt"] = $post["alt"];
    $templateParams["descrizione"] = $post["testo"];
    $templateParams["timestamp"] = date("d/m/Y H:i", strtotime($post["timestamp"]));
    $templateParams["username"] = $post["username"];
    $templateParams["immagineprofilo"] = getUserProfilePic($post["username"], $dbh);
    $templateParams["title"] = "Post di " . $templateParams["username"];
    $templateParams["animals"] = getAnimalsInPost($post["id_post"], $dbh);
    $templateParams["more-comments"] = true;

    echo '<div class="mb-3">';
    require("template\single-post.php");
    echo '</div>';
}
?>
<!-- TODO create own -->
<script src="js/post.js"></script>
