<?php
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
    $templateParams["home"] = true;

    echo '<div class="mb-3">';
    require("single-post.php");
    echo '</div>';
}
?>
