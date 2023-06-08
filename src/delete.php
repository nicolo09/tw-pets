<?php
require_once("bootstrap.php");

$result = 0;

if(isset($_GET["id_post"])) {
    $post = getPost($_GET["id_post"], $dbh);
    if($post["username"] == getUserName($dbh)){
        if($dbh->deletePost($_GET["id_post"])) {
            $result = 1;
            unlink(IMG_DIR . $post["immagine"]);
            $_SESSION["message"] = "Post eliminato con successo!";
        }
    }

    $data = array(
        "result" => $result
    );
    
    echo json_encode($data);
    exit;
}
