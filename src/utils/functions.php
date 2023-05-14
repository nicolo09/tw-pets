<?php

function sec_session_start()
{
    $session_name = 'sec_session_id'; // Imposta un nome di sessione
    $secure = false; // Imposta il parametro a true se vuoi usare il protocollo 'https'.
    $httponly = true; // Questo impedirà ad un javascript di essere in grado di accedere all'id di sessione.
    ini_set('session.use_only_cookies', 1); // Forza la sessione ad utilizzare solo i cookie.
    $cookieParams = session_get_cookie_params(); // Legge i parametri correnti relativi ai cookie.
    session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $secure, $httponly);
    session_name($session_name); // Imposta il nome di sessione con quello prescelto all'inizio della funzione.
    session_start(); // Avvia la sessione php.
    session_regenerate_id(); // Rigenera la sessione e cancella quella creata in precedenza.
}

function isActive($pagename)
{
    //TODO Improve this system
    if (strpos($_SERVER['REQUEST_URI'], $pagename) !== false) {
        echo ("active");
    }
}

function isUserLoggedIn($dbh)
{
    return login_check($dbh);
}

// Login a user by email and password saving the session's cookie
function loginUser($email, $input_password, DatabaseHelper $dbh)
{
    $result = array(false, array());
    //Se l'email è un email
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $user = $dbh->getUser($email);
    }
    //Se l'email è un username
    else {
        $user = $dbh->getUserFromName($email);
    }
    if (count($user) == 1) { // se l'utente esiste
        // Verifichiamo che non sia disabilitato in seguito all'esecuzione di troppi tentativi di accesso errati.
        if (checkBrute($user[0]["username"], $dbh) == true) {
            // Account disabilitato
            // TODO: Invia un e-mail all'utente avvisandolo che il suo account è stato disabilitato.
            // TODO: come gestire la disabilitazione? attributo in persona? 
            $result[1][] = "Il tuo account è stato momentaneamente disabilitato per troppi tentativi di accesso errati. Riprova più tardi.";
            return $result;
        } else {
            if (password_verify($input_password, $user[0]["password"])) { // Verifica che la password memorizzata nel database corrisponda alla password fornita dall'utente.
                // Password corretta!
                $user_browser = $_SERVER['HTTP_USER_AGENT']; // Recupero il parametro 'user-agent' relativo all'utente corrente.
                $username = preg_replace("/[^a-zA-Z0-9_\-]+/", "", $user[0]["username"]); // ci proteggiamo da un attacco XSS
                $_SESSION['username'] = $username;
                $_SESSION['login_string'] = hash('sha512', $user[0]["password"] . $user_browser);
                // Login eseguito con successo.
                $result[0] = true;
                return $result;
            } else {
                // Password incorretta.
                // Registriamo il tentativo fallito nel database.
                $now = time();
                $dbh->addLoginAttempt($user[0]["username"]);
                $result[1][] = "Password errata, se effettui troppi tentativi di accesso il tuo account potrebbe essere bloccato.";
                return $result;
            }
        }
    } else {
        // L'utente inserito non esiste.
        $result[1][] = "Utente o email errati.";
        return $result;
    }
}

function logoutUser($dbh)
{
    // Unset all session values 
    $_SESSION = array();
    // get session parameters 
    $params = session_get_cookie_params();
    // Delete the actual cookie. 
    setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
    // Destroy session 
    session_destroy();
    // Redirect to login page 
    header('Location: login.php');
}

// Check if the user is logged in
function login_check($dbh)
{
    // Verifica che tutte le variabili di sessione siano impostate correttamente
    if (isset($_SESSION['username'], $_SESSION['login_string'])) {
        $login_string = $_SESSION['login_string'];
        $username = $_SESSION['username'];
        $user_browser = $_SERVER['HTTP_USER_AGENT']; // reperisce la stringa 'user-agent' dell'utente.
        $password = $dbh->getPassword($username);

        if (count($password) == 1) { // se l'utente esiste
            $password = $password[0]["password"]; // recupero la password
            $login_check = hash('sha512', $password . $user_browser);
            if ($login_check == $login_string) {
                // Login eseguito
                return true;
            } else {
                // Cookie login_check non corrisponde, login non eseguito
                return false;
            }
        } else {
            // L'utente non esiste (o ne esiste più di uno), login non eseguito
            return false;
        }
    } else {
        // Session cookie non settato, login non eseguito
        return false;
    }
}

function checkBrute($username, $dbh)
{
    $now = time();
    $valid_attempts = $now - (2 * 60 * 60); // Intervallo di tempo equivalente a 2 ore da adesso
    $num_attempts = $dbh->getLoginAttempts($username, $valid_attempts);
    if (count($num_attempts) > 5) { //TODO definire un valore massimo di tentativi
        return true;
    } else {
        return false;
    }
}

function registerAnimal($animal, $type, $file, $description, $owners, $dbh){
    $result = 0;
    $errors = array();
    
    if(strlen($animal) < 3) {
        $errors[] = "Lo username deve essere lungo almeno 3 caratteri.";
    }

    if (strlen($type) < 3) {
        $errors[] = "Il tipo deve essere lungo almeno 3 caratteri.";
    }

    if(count($dbh->getAnimals($animal)) > 0) {
        $errors[] = "Lo username " . $animal . " è già in uso.";
    }

    /* If there are already errors it's useless to upload the image */
    if(count($errors) == 0){
        if(!empty($file["imgprofile"]["name"])) {
            list($imgresult, $msg) = uploadImage(IMG_DIR, $file["imgprofile"]);
            if($imgresult != 0) {
                $img = $msg;
            } else {
                $errors[] = $msg;
            }
        } else {
            $img = DEFAULT_IMG;
        }

        if(count($errors) == 0) {
            if($dbh->addAnimal($animal, $type, $img, $description)) {
                foreach ($owners as $owner) {
                    if(!$dbh->registerOwnership($owner, $animal)){
                        $errors[] = "Impossibile assegnare l'animale a " . $owner . ".";
                    }
                    if(!$dbh->addFollowAnimal($animal, $owner)){ 
                        $errors[] = "Non è stato possibile rendere " . $owner . " follower di " . $animal;
                    }
                }
                if(count($errors) == 0){
                    $result = 1;
                } 
            } else {
                $errors[] = "Si è verificato un problema nell'aggiunta dell'account, riprovare più tardi";
            }
        }
    }
        
    return array($result, $errors);
    
}

function editAnimal($animal, $type, $file, $description, $owners, $dbh){

    $result = 0;
    $errors = array();
       
    if(strlen($type) < 3){
        $errors[] = "Il tipo deve contenere almeno 3 caratteri";
    }

    /* If there are already errors it's useless to upload the image */
    if(count($errors) == 0) {

        if(!empty($file["imgprofile"]["name"])) {
            list($imgresult, $msg) = uploadImage(IMG_DIR, $_FILES["imgprofile"]);
            if($imgresult != 0) {
                $img = $msg;
            } else {
                $errors[] = $msg;
            }
        } else {
            $img = $animal["immagine"];
        }

        if(count($errors) == 0){
            if($dbh->updateAnimal($animal["username"], $type, $img, $description)) { 
                if($img != $animal["immagine"] && $animal["immagine"] != DEFAULT_IMG) {
                    unlink(IMG_DIR . $animal["immagine"]);
                }
                list($result, $errors) = editOwnerships($owners, $animal["username"], $dbh);
            } else {
                $errors[] = "Si è verificato un errore, riprovare più tardi";
            }
        }
    }
        
    
    return array($result, $errors);
}

function editOwnerships($owners, $animal, $dbh) {
    $errors = array();
    $oldOwners = $dbh->getOwners($animal);
    foreach(array_diff($owners, $oldOwners) as $newOwner){
        if(!$dbh->registerOwnership($newOwner, $animal)){
            $errors[] = "Impossibile assegnare l'animale a " . $newOwner . ".";
        }
        if(!$dbh->addFollowAnimal($animal, $newOwner)){ 
            $errors[] = "Non è stato possibile rendere " . $newOwner . " follower di " . $animal;
        }
    }
    foreach(array_diff($oldOwners, $owners) as $deleteOwner){
        if(!$dbh->deleteOwnership($deleteOwner, $animal)){
            $errors[] = "Impossibile rimuovere l'appartenenza di " . $animal . " a " . $deleteOwner . ".";
        }
    }
    $result = count($errors) == 0 ? 1 : 0;
    return array($result, $errors);
}

function uploadImage($path, $image)
{
    $imageName = basename($image["name"]);
    $fullPath = $path . $imageName;

    $maxKB = 500;
    $acceptedExtensions = array("jpg", "jpeg", "png", "gif");
    $result = 0;
    $msg = "";
    //Controllo se immagine è veramente un'immagine
    $imageSize = getimagesize($image["tmp_name"]);
    if ($imageSize === false) {
        $msg .= "File caricato non è un'immagine! ";
    }
    //Controllo dimensione dell'immagine < 500KB
    if ($image["size"] > $maxKB * 1024) {
        $msg .= "File caricato pesa troppo! Dimensione massima è $maxKB KB. ";
    }

    //Controllo estensione del file
    $imageFileType = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
    if (!in_array($imageFileType, $acceptedExtensions)) {
        $msg .= "Accettate solo le seguenti estensioni: " . implode(",", $acceptedExtensions);
    }

    //Controllo se esiste file con stesso nome ed eventualmente lo rinomino
    if (file_exists($fullPath)) {
        $i = 1;
        do {
            $i++;
            $imageName = pathinfo(basename($image["name"]), PATHINFO_FILENAME) . "_$i." . $imageFileType;
        } while (file_exists($path . $imageName));
        $fullPath = $path . $imageName;
    }

    //Se non ci sono errori, sposto il file dalla posizione temporanea alla cartella di destinazione
    if (strlen($msg) == 0) {
        if (!move_uploaded_file($image["tmp_name"], $fullPath)) {
            $msg .= "Errore nel caricamento dell'immagine.";
        } else {
            $result = 1;
            $msg = $imageName;
        }
    }
    return array($result, $msg);
}

function isPasswordStrong($password)
{
    $result = true;
    $errors = array();
    # Check sulla password solo se uguale a conferma password
    if (strlen($password) < 6) {
        $errors[] = "La password deve essere lunga almeno 6 caratteri.";
        $result = false;
    }
    if (!preg_match('@[0-9]@', $password)) {
        $errors[] = "La password deve contenere almeno un numero.";
        $result = false;
    }
    if (!preg_match('@[A-Z]@', $password)) {
        $errors[] = "La password deve contenere almeno una lettera maiuscola.";
        $result = false;
    }
    if (!preg_match('@[a-z]@', $password)) {
        $errors[] = "La password deve contenere almeno una lettera minuscola.";
        $result = false;
    }
    if (!preg_match('@[^\w]@', $password)) {
        $errors[] = "La password deve contenere almeno un carattere speciale.";
        $result = false;
    }
    return array($result, $errors);
}

function register(string $user, string $email, string $password, string $confirm_password, DatabaseHelper $dbh)
{
    $errors = array();
    $result = 0;
    if (strlen($user) < 3) {
        $errors[] = "Lo username deve essere lungo almeno 3 caratteri.";
    }
    if (count($dbh->getUserFromName($user)) > 0) {
        $errors[] = "Lo username è già in uso.";
    }
    if (count($dbh->getUser($email)) > 0) {
        $errors[] = "L'email è già in uso.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email non valida.";
    }
    if ($password != $confirm_password) {
        $errors[] = "Le password non coincidono.";
    } else {
        $passwordStrength = isPasswordStrong($password);
        if (!$passwordStrength[0]) {
            $errors = array_merge($errors, $passwordStrength[1]);
        }
    }
    if (count($errors) == 0) {
        if ($dbh->addUser($user, $password, $email)) {
            $result = 1;
            $headers = 'From: noreply@twpets.com' . "\r\n";
            mail($email, "TWPETS - Registrazione completata", "La registrazione è avvenuta con successo.\n Grazie per esserti registrato su TWPETS!\n Il tuo nome utente è $user", $headers);
        }
    }
    return array($result, $errors);
}

//Returns the username of the user logged in
function getUserName($dbh)
{
    if (isUserLoggedIn($dbh)) {
        return $_SESSION['username'];
    } else {
        return "";
    }
}

function newPost($user, $img, $alt, $txt, $pets, DatabaseHelper $dbh)
{
    $errors = [];
    $uploadErrors = uploadImage(IMG_DIR, $img);
    //Se mette errori stampa, non continuare con query
    if ($uploadErrors[0] == 1) {
        //Non ci sono stati errori di upload, continua con query
        $result = -1; //Not yet set
        if (strlen($alt) <= 50 && strlen($txt) <= 200) {
            $index = $dbh->addPost(basename($img["name"]), $alt, $alt, $user);
            if ($index != -1) {
                //Aggiunta andata a buon fine
                if (!empty($pets)) {
                    foreach ($pets as $single) {
                        $tmp = $dbh->addAnimalToPost($index, $single);
                        if ($tmp == false) {
                            $result = 0;
                            //C'è stato un errore in un inserimento
                            $errors = "C'è stato un errore nell'esecuzione della query sul database";
                        }
                    }
                    if ($result == -1) {
                        //No errori
                        $result = 1;
                    }
                } else {
                    //Non ci sono animali
                    $result = 1;
                }
            } else {
                $result = 0;
                $errors = "C'è stato un errore nell'esecuzione della query sul database";
            }
        } else {
            $result = 0;
            $errors = "La descrizione dell'immagine deve essere di meno di 50 caratteri e il testo meno di 200 caratteri";
        }
    } else {
        //C'è stato un qualche errore con l'upload
        $result = 0;
        $errors = $uploadErrors[1];
    }
    return array($result, $errors);
}

function getManagedAnimals(string $user, DatabaseHelper $dbh)
{
    return $dbh->getOwnedAnimals($user);
}

function getUserData(string $user, DatabaseHelper $dbh)
{
    $tmp=$dbh->getUserInfo($user);
    if(empty($tmp)){
        return array();
    }else{
        #Dato che l'username è univoco, rendo l'array con i dati direttamente accessibile
        return $tmp[0];
    }
}

function getUserCreatedPosts(string $user, DatabaseHelper $dbh)
{
    return $dbh->getUserPosts($user);
}

//Return true if username exists, otherwise false
function doesPersonUsernameExist(string $username, DatabaseHelper $dbh){
    $users=$dbh->doesUserExist($username);
    if(empty($users)){
        return false;
    }else{
        if($users[0]["COUNT(username)"]==1){
            return true;
        }
    }
    return false;
    
}

//Returns true if user owns any animals
function doesUserOwnAnimals(string $username, DatabaseHelper $dbh){
    $animals=$dbh->getOwnedAnimals($username);
    if(count($animals)==0){
        return false;
    }else{
        return true;
    }
    
}

function allFollowers(string $username, DatabaseHelper $dbh){
    $followers=$dbh->getAllFollowers($username);
    $result=array();
    if(empty($followers)==false){
        foreach($followers as $single){
            $result.array_push($single["follower"]);
        }
    }
    return $result;
}

function doesUserFollowMe(string $self, string $follower, DatabaseHelper $dbh){
    $result=$dbh->doesUserFollowMyAccount($self, $follower);
    if($result==1){
        return true;
    }
    return false;
}

//Return 1 if username exists, otherwise 0
function doesAnimalUsernameExist(string $username, DatabaseHelper $dbh){
    $users=$dbh->doesAnimalExist($username);
    if(empty($users)){
        return false;
    }else{
        if($users[0]["COUNT(username)"]==1){
            return true;
        }
    }
    return false;
    
}

function getAnimalData(string $user, DatabaseHelper $dbh)
{
    $tmp=$dbh->getAnimalInfo($user);
    if(empty($tmp)){
        return array();
    }else{
        #Dato che l'username è univoco, rendo l'array con i dati direttamente accessibile
        return $tmp[0];
    }
}

function getAnimalRelatedPosts(string $username, DatabaseHelper $dbh)
{
    return $dbh->getAnimalPosts($username);
}

//Ritorna vero se l'utente segue l'animale dato
function doIFollowAnimal(string $username, string $animal, DatabaseHelper $dbh){
    $result=$dbh->doesUserFollowAnimal($username, $animal);
    if(empty($result)){
        return false;
    }else{
        return true;
    }

}

function followPerson(string $followed, string $follower, DatabaseHelper $dbh){
    return $dbh->addFollowPerson($followed, $follower);
}

function unfollowPerson(string $followed, string $follower, DatabaseHelper $dbh){
    return $dbh->removeFollowPerson($followed, $follower);
}


function followAnimal(string $animal, string $follower, DatabaseHelper $dbh){
    return $dbh->addFollowAnimal($animal, $follower);
}

function unfollowAnimal(string $animal, string $follower, DatabaseHelper $dbh){
    return $dbh->removeFollowAnimal($animal, $follower);
}
function changePassword(string $oldPassword, string $newPassword, string $confirmPassword, DatabaseHelper $dbh)
{
    $errors = [];
    $result = false;
    if ($newPassword != $confirmPassword) {
        $errors[] = "Le password non coincidono.";
    } else {
        $passwordStrength = isPasswordStrong($newPassword);
        if (!$passwordStrength[0]) {
            $errors = array_merge($errors, $passwordStrength[1]);
        }
    }
    if (count($errors) == 0) {
        $user = $dbh->getUserFromName($_SESSION['username']);
        if (password_verify($oldPassword, $user[0]['password'])) {
            $result = $dbh->changePassword($_SESSION['username'], $newPassword);
        } else {
            $errors[] = "La password attuale non è corretta.";
        }
    }
    return array($result, $errors);
}

function getPost(int $id, DatabaseHelper $dbh){
    $result=$dbh->getPostInfo($id);
    if(empty($result)==false){
        //Visto che l'id è univoco per post, è inutile avere un array di un array di un singolo risultato
        return $result[0];
    }else{
        return $result;
    }
}

function getLikes(int $id, DatabaseHelper $dbh){
    $result=$dbh->getPostLikes($id);
    if(empty($result)){
        return 0;
    }else{
        return $result[0]["COUNT(*)"];
    }
}

function isPostLikedBy(int $id, string $username, DatabaseHelper $dbh){
    $result=$dbh->doesUserLikePost($id, $username);
    if(empty($result)){
        return false;
    }else{
        return $result[0]["COUNT(*)"]==1;
    }
}

function isPostSavedBy(int $id, string $username, DatabaseHelper $dbh){
    $result=$dbh->hasUserSavedPost($id, $username);
    if(empty($result)){
        return false;
    }else{
        return $result[0]["COUNT(*)"]==1;
    }
}

function isIdPostValid(int $id, DatabaseHelper $dbh){
    $result=$dbh->isIdPostCorrect($id);
    if(empty($result)){
        return false;
    }else{
        return $result[0]["COUNT(*)"]==1;
    }
}