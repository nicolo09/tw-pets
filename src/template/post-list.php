<?php
foreach ($posts as $result) {
    $templateParams["id"] = $result["id_post"];
    $templateParams["immagine"] = IMG_DIR . $result["immagine"];
    $templateParams["alt"] = $result["alt"];
    $templateParams["descrizione"] = $result["testo"];
    $templateParams["timestamp"] = date("d/m/Y H:i", strtotime($result["timestamp"]));
    $templateParams["username"] = $result["username"];
    $templateParams["immagineprofilo"] = IMG_DIR . $dbh->getUserFromName($result["username"])[0]["immagine"];
    $templateParams["title"] = "Post di " . $templateParams["username"];
    $templateParams["animals"] = getAnimalsInPost($result["id_post"], $dbh);
    //I commenti vengono caricati da Javascript
    echo '<div class="mb-3">';
    require("template\single-post.php");
    echo '</div>';
}
