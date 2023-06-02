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

/**
 * Registers a new animal on the database.
 * @param string $animal the animal's username.
 * @param string $type the animal's type.
 * @param array $file an array containing the animal's image.
 * @param string $description the animal's description.
 * @param array $owners a list of the current animal's owners.
 * @param DatabaseHelper $dbh object to communicate with the database.
 * @return int 0 if there were errors, 1 otherwise.
 * @return array a string array of all the errors that occurred, it is empty if there were none.
 */
function registerAnimal($animal, $type, $file, $description, $owners, $dbh)
{
    $result = 0;
    $errors = array();

    if (strlen($animal) < 3) {
        $errors[] = "Lo username deve essere lungo almeno 3 caratteri.";
    }

    if (!isUserID($animal)) {
        $errors[] = "Lo username può contenere solo lettere, numeri e _";
    }

    if (strlen($type) < 3) {
        $errors[] = "Il tipo deve essere lungo almeno 3 caratteri.";
    }

    if (count($dbh->getAnimalFromName($animal)) > 0) {
        $errors[] = "Lo username " . $animal . " è già in uso.";
    }

    /* If there are already errors it's useless to upload the image */
    if (count($errors) == 0) {
        if (!empty($file["imgprofile"]["name"])) {
            list($imgresult, $msg) = uploadImage(IMG_DIR, $file["imgprofile"]);
            if ($imgresult != 0) {
                $img = $msg;
            } else {
                $errors[] = $msg;
            }
        } else {
            $img = DEFAULT_PET_IMG;
        }

        if (count($errors) == 0) {
            if ($dbh->addAnimal($animal, $type, $img, $description)) {
                foreach ($owners as $owner) {
                    if (!$dbh->registerOwnership($owner, $animal)) {
                        $errors[] = "Impossibile assegnare l'animale a " . $owner . ".";
                    }
                    if (!$dbh->addFollowAnimal($animal, $owner)) {
                        $errors[] = "Non è stato possibile rendere " . $owner . " follower di " . $animal;
                    }
                }
                if (count($errors) == 0) {
                    $result = 1;
                }
            } else {
                $errors[] = "Si è verificato un problema nell'aggiunta dell'account, riprovare più tardi";
            }
        }
    }

    return array($result, $errors);
}

/**
 * Edits an existing animal on the database.
 * @param string $animal the animal's username.
 * @param string $type the animal's type.
 * @param array $file an array containing the animal's image.
 * @param string $description the animal's description.
 * @param array $owners a list of the current animal's owners.
 * @param DatabaseHelper $dbh object to communicate with the database.
 * @return int 0 if there were errors, 1 otherwise.
 * @return array a string array of all the errors that occurred, it is empty if there were none.
 */
function editAnimal($animal, $type, $file, $description, $owners, $dbh)
{

    $result = 0;
    $errors = array();

    if (strlen($type) < 3) {
        $errors[] = "Il tipo deve contenere almeno 3 caratteri";
    }

    /* If there are already errors it's useless to upload the image */
    if (count($errors) == 0) {

        if (!empty($file["imgprofile"]["name"])) {
            list($imgresult, $msg) = uploadImage(IMG_DIR, $_FILES["imgprofile"]);
            if ($imgresult != 0) {
                $img = $msg;
            } else {
                $errors[] = $msg;
            }
        } else {
            $img = $animal["immagine"];
        }

        if (count($errors) == 0) {
            if ($dbh->updateAnimal($animal["username"], $type, $img, $description)) {
                if ($img != $animal["immagine"] && $animal["immagine"] != DEFAULT_PET_IMG) {
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

/**
 * Updates the owners list of an animal.
 * @param array $owners the updated list of owners.
 * @param string $animal the animal username.
 * @param DatabaseHelper $dbh object to communicate with the database.
 * @return int 0 if there were errors, 1 otherwise.
 * @return array a string array of all the errors that occurred, it is empty if there were none.
 */
function editOwnerships($owners, $animal, $dbh)
{
    $errors = array();
    $oldOwners = array_column($dbh->getOwners($animal), "username");
    foreach (array_diff($owners, $oldOwners) as $newOwner) {
        if (!doIFollowAnimal($newOwner, $animal, $dbh)) {
            if (!$dbh->addFollowAnimal($animal, $newOwner)) {
                $errors[] = "Non è stato possibile rendere " . $newOwner . " follower di " . $animal;
            }
        }
        if (!$dbh->registerOwnership($newOwner, $animal)) {
            $errors[] = "Impossibile assegnare l'animale a " . $newOwner . ".";
        }
    }
    foreach (array_diff($oldOwners, $owners) as $deleteOwner) {
        if (!$dbh->deleteOwnership($deleteOwner, $animal)) {
            $errors[] = "Impossibile rimuovere l'appartenenza di " . $animal . " a " . $deleteOwner . ".";
        }
    }
    $result = count($errors) == 0 ? 1 : 0;
    return array($result, $errors);
}

/**
 * Uploads an image in a given directory.
 * @param string $path where the image will be uploaded to.
 * @param array $image an associative array containing a file.
 * @return int 0 if there were errors, 1 otherwise.
 * @return string a string containing all the errors that occurred, if there were none it contains the image's name.
 */
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

function isUserID($username)
{
    if (preg_match('/^[a-z\d_]{2,20}$/i', $username)) {
        return true;
    } else {
        return false;
    }
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
    if (isUserID($user) == false) {
        $errors[] = "Lo username può contenere solo lettere, numeri e _";
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

/**
 * Edits an existing user on the database.
 * @param string $user the person's username.
 * @param string $employment the user's employment.
 * @param array $file an array containing the user's image.
 * @param string $description the user's description.
 * @param DatabaseHelper $dbh object to communicate with the database.
 * @return int 0 if there were errors, 1 otherwise.
 * @return array a string array of all the errors that occurred, it is empty if there were none.
 */
function editUserProfile(string $user, string $employment, array $file, string $description, DatabaseHelper $dbh)
{
    $errors = array();
    $result = 0;
    $oldImage = $dbh->getUserFromName($user)[0]["immagine"];

    if (!doesPersonUsernameExist($user, $dbh)) {
        $errors[] = "Non è possibile modificare un account inesistente";
    }
    if (!empty($employment) && strlen($employment) < 3) {
        $errors[] = "L'impiego deve essere lungo almeno 3 caratteri";
    }

    if (count($errors) == 0) {
        if (!empty($file["imgprofile"]["name"])) {
            list($imgresult, $msg) = uploadImage(IMG_DIR, $_FILES["imgprofile"]);
            if ($imgresult != 0) {
                $img = $msg;
            } else {
                $errors[] = $msg;
            }
        } else {
            $img = $oldImage;
        }

        if (count($errors) == 0) {
            if ($dbh->updateUserProfile($user, $employment, $img, $description)) {
                if ($img != $oldImage && $oldImage != DEFAULT_USER_IMG) {
                    unlink(IMG_DIR . $oldImage);
                }
                $result = 1;
            } else {
                $errors[] = "Si è verificato un errore, riprovare più tardi";
            }
        }
    }

    return array($result, $errors);
}

/**
 * Returns the username of the user
 * @param DatabaseHelper $dbh the database where the info is saved
 * @return string of the username or "" if no user is logged in
 */
function getUserName(DatabaseHelper $dbh)
{
    if (isUserLoggedIn($dbh)) {
        return $_SESSION['username'];
    } else {
        return "";
    }
}

/**
 * Creates a new post
 * @param string $user the username of the user creating the post
 * @param array $img the file of the image of the new post
 * @param string $alt the description of the image
 * @param string $txt the description of the new post
 * @param array $pets the pets to include in the new post
 * @param DatabaseHelper $dbh the database where the info is saved
 * @return array where result is 1 if creation worked and error is error messages
 */
function newPost(string $user, array $img, string $alt, string $txt, array $pets, DatabaseHelper $dbh)
{
    $errors = [];
    $uploadErrors = uploadImage(IMG_DIR, $img);
    //Se mette errori stampa, non continuare con query
    if ($uploadErrors[0] == 1) {
        //Non ci sono stati errori di upload, continua con query
        $imgUp = $uploadErrors[1]; //Se il file è stato rinominato, devo caricare il file corretto con nome rinominato
        $result = -1; //Not yet set
        if (strlen($alt) <= 50 && strlen($txt) <= 200) {
            $index = $dbh->addPost($imgUp, $alt, $txt, $user);
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

/**
 * Returns the managed animals of a user
 * @param string $user the username of the user
 * @param DatabaseHelper $dbh the database where the info is saved
 * @return array of managed animals
 */
function getManagedAnimals(string $user, DatabaseHelper $dbh)
{
    return $dbh->getOwnedAnimals($user);
}

/**
 * Returns user information
 * @param string $user the username of the user
 * @param DatabaseHelper $dbh the database where the info is saved
 * @return array of user information
 */
function getUserData(string $user, DatabaseHelper $dbh)
{
    $tmp = $dbh->getUserInfo($user);
    if (empty($tmp)) {
        return array();
    } else {
        #Dato che l'username è univoco, rendo l'array con i dati direttamente accessibile
        return $tmp[0];
    }
}

/**
 * Returns all posts made by a user
 * @param string $user the username of the user
 * @param DatabaseHelper $dbh the database where the info is saved
 * @return array of posts
 */
function getUserCreatedPosts(string $user, DatabaseHelper $dbh)
{
    return $dbh->getUserPosts($user);
}

/**
 * Checks if a person username exists
 * @param string $username the username of the person
 * @param DatabaseHelper $dbh the database where the info is saved
 * @return bool true if username exists
 */
function doesPersonUsernameExist(string $username, DatabaseHelper $dbh)
{
    $users = $dbh->doesUserExist($username);
    if (empty($users)) {
        return false;
    } else {
        if ($users[0]["COUNT(username)"] == 1) {
            return true;
        }
    }
    return false;
}

/**
 * Checks if a person owns any animals
 * @param string $user the username of the person
 * @param DatabaseHelper $dbh the database where the info is saved
 * @return bool true if user owns any animals
 */
function doesUserOwnAnimals(string $username, DatabaseHelper $dbh)
{
    $animals = $dbh->getOwnedAnimals($username);
    if (count($animals) == 0) {
        return false;
    } else {
        return true;
    }
}
/**
 * Returns all the followers of a given user
 * @param string $username the username of the person
 * @param DatabaseHelper $dbh the database where the info is saved
 * @return array of followers
 */
function allFollowers(string $username, DatabaseHelper $dbh)
{
    $followers = $dbh->getAllFollowers($username);
    $result = array();
    if (empty($followers) == false) {
        foreach ($followers as $single) {
            $result . array_push($single["follower"]);
        }
    }
    return $result;
}

/**
 * Checks if a user follows another
 * @param string $self the username of followed
 * @param string $follower the username of the follower
 * @param DatabaseHelper $dbh the database where the info is saved
 * @return bool true if follower follows followed
 */
function doesUserFollowMe(string $self, string $follower, DatabaseHelper $dbh)
{
    $result = $dbh->doesUserFollowMyAccount($self, $follower);
    if ($result == 1) {
        return true;
    }
    return false;
}

/**
 * Checks if a animal username exists
 * @param string $username the username of the animal
 * @param DatabaseHelper $dbh the database where the info is saved
 * @return bool true if username exists
 */
function doesAnimalUsernameExist(string $username, DatabaseHelper $dbh)
{
    $users = $dbh->doesAnimalExist($username);
    if (empty($users)) {
        return false;
    } else {
        if ($users[0]["COUNT(username)"] == 1) {
            return true;
        }
    }
    return false;
}

/**
 * Returns animal information
 * @param string $user the username of the animal
 * @param DatabaseHelper $dbh the database where the info is saved
 * @return array of animal information
 */
function getAnimalData(string $user, DatabaseHelper $dbh)
{
    $tmp = $dbh->getAnimalInfo($user);
    if (empty($tmp)) {
        return array();
    } else {
        #Dato che l'username è univoco, rendo l'array con i dati direttamente accessibile
        return $tmp[0];
    }
}

/**
 * Returns all posts of an animal
 * @param string $username the username of the animal
 * @param DatabaseHelper $dbh the database where the info is saved
 * @return array of posts
 */
function getAnimalRelatedPosts(string $username, DatabaseHelper $dbh)
{
    return $dbh->getAnimalPosts($username);
}

/**
 * Checks if a user follows an animal
 * @param string $username the username of user
 * @param string $animal the username of the animal being followed
 * @param DatabaseHelper $dbh the database where the info is saved
 * @return bool true if user follows animal
 */
function doIFollowAnimal(string $username, string $animal, DatabaseHelper $dbh)
{
    $result = $dbh->doesUserFollowAnimal($username, $animal);
    if (empty($result)) {
        return false;
    } else {
        return true;
    }
}

/**
 * Makes a person follow another
 * @param string $followed the username of followed
 * @param string $follower the username of the follower
 * @param DatabaseHelper $dbh the database where the info is saved
 * @return bool false if something went wrong
 */
function followPerson(string $followed, string $follower, DatabaseHelper $dbh)
{
    return $dbh->addFollowPerson($followed, $follower);
}

/**
 * Makes a person unfollow another
 * @param string $followed the username of followed
 * @param string $follower the username of the follower
 * @param DatabaseHelper $dbh the database where the info is saved
 * @return bool false if something went wrong
 */
function unfollowPerson(string $followed, string $follower, DatabaseHelper $dbh)
{
    return $dbh->removeFollowPerson($followed, $follower);
}

/**
 * Makes a person follow an animal
 * @param string $animal the username of animal
 * @param string $follower the username of the follower
 * @param DatabaseHelper $dbh the database where the info is saved
 * @return bool false if something went wrong
 */
function followAnimal(string $animal, string $follower, DatabaseHelper $dbh)
{
    return $dbh->addFollowAnimal($animal, $follower);
}

/**
 * Makes a person unfollow an animal
 * @param string $animal the username of animal
 * @param string $follower the username of the follower
 * @param DatabaseHelper $dbh the database where the info is saved
 * @return bool false if something went wrong
 */
function unfollowAnimal(string $animal, string $follower, DatabaseHelper $dbh)
{
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

/**
 * Returns all info about a post
 * @param int $id the post id
 * @param DatabaseHelper $dbh the database where the info is saved
 * @return array of post info
 */
function getPost(int $id, DatabaseHelper $dbh)
{
    $result = $dbh->getPostInfo($id);
    if (empty($result) == false) {
        //Visto che l'id è univoco per post, è inutile avere un array di un array di un singolo risultato
        return $result[0];
    } else {
        return $result;
    }
}

/**
 * Returns number of likes of a post
 * @param int $id the post id
 * @param DatabaseHelper $dbh the database where the info is saved
 * @return int number of likes
 */
function getLikes(int $id, DatabaseHelper $dbh)
{
    $result = $dbh->getPostLikes($id);
    if (empty($result)) {
        return 0;
    } else {
        return $result[0]["COUNT(*)"];
    }
}

/**
 * Returns if a user has liked a post
 * @param int $id the post id
 * @param string $username the user
 * @param DatabaseHelper $dbh the database where the info is saved
 * @return bool true if user has liked the post
 */
function isPostLikedBy(int $id, string $username, DatabaseHelper $dbh)
{
    $result = $dbh->doesUserLikePost($id, $username);
    if (empty($result)) {
        return false;
    } else {
        return $result[0]["COUNT(*)"] == 1;
    }
}

/**
 * Returns if a user has saved a post
 * @param int $id the post id
 * @param string $username the user
 * @param DatabaseHelper $dbh the database where the info is saved
 * @return bool true if user has saved the post
 */
function isPostSavedBy(int $id, string $username, DatabaseHelper $dbh)
{
    $result = $dbh->hasUserSavedPost($id, $username);
    if (empty($result)) {
        return false;
    } else {
        return $result[0]["COUNT(*)"] == 1;
    }
}

/**
 * Returns if a post id exists
 * @param int $id the post id
 * @param DatabaseHelper $dbh the database where the info is saved
 * @return bool true if post exist
 */
function isIdPostValid(int $id, DatabaseHelper $dbh)
{
    $result = $dbh->isIdPostCorrect($id);
    if (empty($result)) {
        return false;
    } else {
        return $result[0]["COUNT(id_post)"] == 1;
    }
}

/**
 * Makes a person like a post
 * @param int $id the post id
 * @param string $username the username of the user
 * @param DatabaseHelper $dbh the database where the info is saved
 * @return bool false if something went wrong
 */
function likePost(int $id, string $username, DatabaseHelper $dbh)
{
    return $dbh->addLikePost($id, $username);
}

/**
 * Makes a person unlike a post
 * @param int $id the post id
 * @param string $username the username of the user
 * @param DatabaseHelper $dbh the database where the info is saved
 * @return bool false if something went wrong
 */
function unLikePost(int $id, string $username, DatabaseHelper $dbh)
{
    return $dbh->removeLikePost($id, $username);
}

/**
 * Makes a person save a post
 * @param int $id the post id
 * @param string $username the username of the user
 * @param DatabaseHelper $dbh the database where the info is saved
 * @return bool false if something went wrong
 */
function savePost(int $id, string $username, DatabaseHelper $dbh)
{
    return $dbh->addSavePost($id, $username);
}

/**
 * Makes a person unsave a post
 * @param int $id the post id
 * @param string $username the username of the user
 * @param DatabaseHelper $dbh the database where the info is saved
 * @return bool false if something went wrong
 */
function unSavePost(int $id, string $username, DatabaseHelper $dbh)
{
    return $dbh->removeSavePost($id, $username);
}

/**
 * Returns all animals in a post
 * @param int $id the post id
 * @param DatabaseHelper $dbh the database where the info is saved
 * @return array of animals
 */
function getAnimalsInPost(int $id, DatabaseHelper $dbh)
{
    $final = array();
    $result = $dbh->getTaggedAnimals($id);
    foreach ($result as $animal) {
        $final[] = $animal["animale"];
    }
    return $final;
}

/**
 * Ritorna l'href per la pagina di un profilo utente di una persona
 * @param string $username username della persona
 * @return string href
 */
function getUserProfileHref(string $username)
{
    return "view-user-profile.php?username=" . $username . "&type=person";
}

/**
 * Ritorna l'href per la pagina di un profilo utente di un animale
 * @param string $username username della persona
 * @return string href
 */
function getAnimalProfileHref(string $username)
{
    return "view-user-profile.php?username=" . $username . "&type=animal";
}

/**
 * Returns user profile href reference 
 * @param string $username the user's username
 * @param string $type defines if the user is an animal or a person
 * @return string href
 */
function getProfileHref(string $username, string $type)
{
    return "view-user-profile.php?username=" . $username . "&type=" . $type;
}

/**
 * Ritorna l'href per la pagina di visualizzazione di un post
 * @param int $id id del post
 * @return string href
 */
function getPostHref(int $id)
{
    return "view-post-profile.php?id=" . $id;
}
/**
 * Ritorna l'src dell'immagine di profilo di un utente
 * @param string $user username dell'utente
 * @return string src
 */
function getUserProfilePic(string $user, DatabaseHelper $dbh)
{
    $result = $dbh->getUserFromName($user);
    if (empty($result)) {
        return "img/default.jpg";
    } else {
        return "img/" . $result[0]["immagine"];
    }
}

/**
 * Ritorna i n commenti più recenti lasciati sul post
 * @param int $id_post post di cui si vogliono caricare i commenti
 * @param int $n numero commenti da caricare
 * @param DatabaseHelper $dbh il database in cui sono salvati i commenti
 * @return array vettore di commenti
 */
function loadMostRecentComments(int $id_post, int $n, DatabaseHelper $dbh)
{
    return $dbh->getMostRecentComments($id_post, $n);
}

/**
 * Ritorna tutti commenti in ordine dai più recenti lasciati sul post
 * @param int $id_post post di cui si vogliono caricare i commenti
 * @param DatabaseHelper $dbh il database in cui sono salvati i commenti
 * @return array vettore di commenti
 */
function allLoadMostRecentComments(int $id_post, DatabaseHelper $dbh)
{
    return $dbh->getAllMostRecentComments($id_post);
}

/**
 * Ritorna se il commento ha "commenti figli"
 * @param int $id_comment l'identificatore del commento padre
 * @param DatabaseHelper $dbh il database in cui sono salvati i commenti
 * @return true se il commento ha "commenti figli"
 */
function doesCommentHaveComments(int $id_comment, DatabaseHelper $dbh)
{
    $result = $dbh->doesCommentHaveAnswers($id_comment);
    if (empty($result)) {
        return false;
    } else {
        return $result[0]["COUNT(*)"] > 0;
    }
}


/**
 * Ritorna i dati del commento
 * @param int $id l'identificatore del commento
 * @param DatabaseHelper $dbh il database in cui sono salvati i commenti
 * @return array di dati del commento preso in input
 */
function getCommentInfo(int $id, DatabaseHelper $dbh)
{
    $result = $dbh->getComment($id);
    if (empty($result)) {
        return array();
    } else {
        return $result[0];
    }
}

/**
 * Inserisce un commento
 * @param string $username l'utente che crea il commento
 * @param string $text il testo del commento
 * @param int $id_post il post a cui fa il commento
 * @param DatabaseHelper $dbh il database in cui sono salvati i commenti
 * @return bool true se l'inserimento è andato a buon fine
 */
function newComment(string $username, string $text, int $id_post, DatabaseHelper $dbh)
{
    return $dbh->addNewComment($username, $text, $id_post);
}

/**
 * Inserisce un commento
 * @param string $username l'utente che crea il commento
 * @param string $text il testo del commento
 * @param int $id_post il post a cui fa il commento
 * @param int $id_padre il commento a cui risponde
 * @param DatabaseHelper $dbh il database in cui sono salvati i commenti
 * @return bool true se l'inserimento è andato a buon fine
 */
function newCommentAnswer(string $username, int $id_padre, string $text, int $id_post, DatabaseHelper $dbh)
{
    return $dbh->addNewCommentToComment($username, $id_padre, $text, $id_post);
}

/**
 * Ritorna n commenti del post con offset più vecchi del timestamp fornito
 * @param int $id del post
 * @param int $n numero commenti da caricare
 * @param int $offset l'offset dei commenti
 * @param string $timestamp del commento più recente
 * @param DatabaseHelper $dbh il database in cui sono salvati i commenti
 * @return array i commenti
 */
function getRecentComments(int $id, int $n, int $offset, string $timestamp, DatabaseHelper $dbh)
{
    return $dbh->getCommentOffset($id, $n, $offset, $timestamp);
}

/**
 * Ritorna tutti commenti in ordine dai più recenti lasciati sul post
 * @param int $id_post post di cui si vogliono caricare i commenti
 * @param string $timestamp del commento più recente
 * @param DatabaseHelper $dbh il database in cui sono salvati i commenti
 * @return array vettore di commenti
 */
function allLoadMostRecentCommentsAfter(int $id_post, string $timestamp, DatabaseHelper $dbh)
{
    return $dbh->getAllMostRecentCommentsAfter($id_post, $timestamp);
}

/**
 * Ritorna n commenti in risposta al post con offset più vecchi del timestamp fornito
 * @param int $id del post
 * @param int $id_comment del commento
 * @param int $n numero commenti da caricare
 * @param int $offset l'offset dei commenti
 * @param string $timestamp del commento più recente
 * @param DatabaseHelper $dbh il database in cui sono salvati i commenti
 * @return array i commenti
 */
function getRecentCommentsAnswers(int $id, int $id_comment, int $n, int $offset, string $timestamp, DatabaseHelper $dbh)
{
    return $dbh->getCommentAnswerOffset($id, $id_comment, $n, $offset, $timestamp);
}

/**
 * Returns the profile picture of an animal
 * @param string $animal the animal's username
 * @param DatabaseHelper $dbh the database helper
 */
function getAnimalProfilePic(string $animal, DatabaseHelper $dbh)
{
    $result = $dbh->getAnimalInfo($animal);
    if (empty($result)) {
        return "img/default.jpg";
    } else {
        return "img/" . $result[0]["immagine"];
    }
}

/**
 * Ritorna il numero di commenti presenti nella directory con estensione 
 * @param string $directory la directory
 * @param string $l'estensione ammessa da contare
 * @return int numero file
 */
function countNFiles(string $directory, string  $extension)
{
    $directory = new DirectoryIterator($directory);
    $i = 0;
    foreach ($directory as $file) {
        if ($file->isFile()) {
            if ($file->getExtension() == $extension)
                $i++;
        }
    }
    return $i;
}
