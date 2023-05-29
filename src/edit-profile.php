<?php
require_once("bootstrap.php");

if(!login_check($dbh)){
    header("Location: login.php");
    exit;
}

$user = getUserName($dbh);

if(isset($_GET["action"])){
    //Must update user data
    $employment = htmlspecialchars($_POST["employment"]);
    $description = htmlspecialchars($_POST["description"]);

    list($result, $errors) = editUserProfile($user, $employment, $_FILES, $description, $dbh);
    if($result == 1){
        //Updated profile successfully
        $_SESSION["message"] = "Profilo modificato con successo";
        header("Location: view-user-profile.php");
        exit;
    } else {
        //Something went wrong
        $templateParams["errors"] = $errors;
    }

} 
//Must load current values of the user
$data = getUserData($user, $dbh);

if(empty($data)) {
    //Couldn't load user data
    $_SESSION["error"] = "Si è verificato un problema di comunicazione col server, verificare la connessione";
    header("Location: tab-profile.php");
    exit;
}

$templateParams["username"] = $data["username"];
$templateParams["employment"] = $data["impiego"];
$templateParams["img"] = IMG_DIR . $data["immagine"];
$templateParams["description"] = $data["descrizione"];
    


$templateParams["page"] = "edit-profile-form.php";
$templateParams["title"] = "Modifica - " . $user;
$templateParams["subtitle"] = "Modifica il tuo profilo!";
require_once("template/base.php");
?>
