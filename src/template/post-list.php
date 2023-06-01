<?php
foreach ($posts as $result) {
    
    $templateParams["id"] = $result["id_post"];
    $templateParams["immagine"] = IMG_DIR . $result["immagine"];
    $templateParams["alt"] = $result["alt"];
    $templateParams["descrizione"] = $result["testo"];
    $templateParams["timestamp"] = date("d/m/Y H:i", strtotime($result["timestamp"]));
    $templateParams["username"] = $result["username"];
    $templateParams["immagineprofilo"] = getUserProfilePic($result["username"], $dbh);
    $templateParams["title"] = "Post di " . $templateParams["username"];
    $templateParams["animals"] = getAnimalsInPost($result["id_post"], $dbh);
    $templateParams["more-comments"] = true;
    $templateParams["home"] = true;

    echo '<div class="mb-3">';
    require("single-post.php");
    echo '</div>';
}
?>
