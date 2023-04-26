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
    if (basename($_SERVER['PHP_SELF']) == $pagename) {
        echo " class='active' ";
    }
}

function isUserLoggedIn()
{
    //TODO: implementare
    return !empty($changethis);
}

// Login a user by email and password saving the session's cookie
function loginUser($email, $input_password, $dbh)
{
    $user = $dbh->getUser($email);
    $input_password = password_hash($input_password, PASSWORD_DEFAULT);
    if (count($user) == 1) { // se l'utente esiste
        // Verifichiamo che non sia disabilitato in seguito all'esecuzione di troppi tentativi di accesso errati.
        if (checkBrute($user[0]["username"], $dbh) == true) {
            // Account disabilitato
            // TODO: Invia un e-mail all'utente avvisandolo che il suo account è stato disabilitato.
            // TODO: come gestire la disabilitazione? attributo in persona? 
            return false;
        } else {
            if ($user[0]["password"] == $input_password) { // Verifica che la password memorizzata nel database corrisponda alla password fornita dall'utente.
                // Password corretta!
                $user_browser = $_SERVER['HTTP_USER_AGENT']; // Recupero il parametro 'user-agent' relativo all'utente corrente.
                $username = preg_replace("/[^a-zA-Z0-9_\-]+/", "", $user[0]["username"]); // ci proteggiamo da un attacco XSS
                $_SESSION['username'] = $username;
                $_SESSION['login_string'] = hash('sha512', $input_password . $user_browser);
                // Login eseguito con successo.
                return true;
            } else {
                // Password incorretta.
                // Registriamo il tentativo fallito nel database.
                $now = time();
                $dbh->addLoginAttempt($user[0]["username"], $now);
                return false;
            }
        }
    } else {
        // TODO: L'utente inserito non esiste.
        return false;
    }
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

function register(string $user, string $password, string $email, string $confirmemail, DatabaseHelper $dbh)
{
    $errors = array();
    if (strlen($user) < 3) {
        $errors[] = "Lo username deve essere lungo almeno 3 caratteri.";
    }
    if (count($dbh->getUser($user)) > 0) {
        $errors[] = "Lo username è già in uso.";
    }
    if ($email != $confirmemail) {
        $errors[] = "Le email non corrispondono.";
    }
    if (count($dbh->getUser($email)) > 0) {
        $errors[] = "L'email è già in uso.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email non valida.";
    }
    if (strlen($password) < 6) {
        $errors[] = "La password deve essere lunga almeno 6 caratteri.";
    }
    if (!preg_match('@[0-9]@', $password)) {
        $errors[] = "La password deve contenere almeno un numero.";
    }
    if (!preg_match('@[A-Z]@', $password)) {
        $errors[] = "La password deve contenere almeno una lettera maiuscola.";
    }
    if (!preg_match('@[a-z]@', $password)) {
        $errors[] = "La password deve contenere almeno una lettera minuscola.";
    }
    if (!preg_match('@[^\w]@', $password)) {
        $errors[] = "La password deve contenere almeno un carattere speciale.";
    }
    if (count($errors) == 0) {
        if ($dbh->addUser($user, $password, $email)) {
            $result = 1;
        }
    }
    return array($result, $errors);
}
