<?php
require_once("settings-utils.php");
/**
 * Starts a secure session.
 */
function sec_session_start()
{
    $session_name = 'sec_session_id'; // Choose a session name
    $secure = false; // Set true if you want to use https
    $httponly = true; // Preventing javascript to access session's id.
    ini_set('session.use_only_cookies', 1); // Force the session to use only cookies.
    $cookieParams = session_get_cookie_params(); // Reads current cookies' parameters.
    session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $secure, $httponly);
    session_name($session_name); // Sets the session name as the one chosen at the start.
    session_start(); // Starts the php session.
    session_regenerate_id(); // Regenerates the session and cancels the previous one.
}

function isActive($pagename)
{
    if (strpos($_SERVER['REQUEST_URI'], $pagename) !== false) {
        echo ("active");
    }
}

/**
 * Login a user by email and password saving the session's cookie.
 * @param string $email the email or username inserted by the user.
 * @param string $input_password the password inserted by the user.
 * @param DatabaseHelper $dbh object that can communicate with the database.
 */
function loginUser(string $email, string $input_password, DatabaseHelper $dbh)
{
    $result = array(false, array());
    //Checks if $email is an email
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $user = $dbh->getUser($email);
    }
    //If it's not it must be the username
    else {
        $user = $dbh->getUserFromName($email);
    }
    if (count($user) == 1) { // Checks if the user exist
        // Checking if the user's account is disabled because of too many failed access attempts.
        if (checkBrute($user[0]["username"], $dbh) == true||$dbh->isAccountDisabled($user[0]["username"])) {
            // The account is disabled
            sendEmailAboutDisabledAccount($user[0]["email"]);
            $dbh->disableAccount($user[0]["username"]);
            $result[1][] = "Il tuo account è stato disabilitato per troppi tentativi di accesso errati. Chiedi il reset della password.";
            return $result;
        } else {
            if (password_verify($input_password, $user[0]["password"])) { // Checking if the password in the database and the one inserted by the user are equal
                // The password is correct
                $user_browser = $_SERVER['HTTP_USER_AGENT']; // Retrieving 'user-agent' parameter relative to the current user.
                $username = preg_replace("/[^a-zA-Z0-9_\-]+/", "", $user[0]["username"]); // Protecting from XSS attacks
                $_SESSION['username'] = $username;
                $_SESSION['login_string'] = hash('sha512', $user[0]["password"] . $user_browser);
                // Login successful
                $result[0] = true;
                //Notify user on mail 
                if (isNotificationNewLoginEnabled($username, $dbh)) {
                    // The message
                    $message = "È stato eseguito un nuovo accesso al tuo account twpets.\nSe non lo hai effettuato tu richiedi un reset della password dal sito.\n\nTWPETS";
                    $headers = 'From: noreply@twpets.com' . "\r\n";
                    // Sending the email
                    mail(getUserData($username, $dbh)["email"], "TWPETS - Nuovo accesso", $message, $headers);
                }
                return $result;
            } else {
                // Wrong password 
                // The failed attempt gets registered in the database
                $now = time();
                $dbh->addLoginAttempt($user[0]["username"]);
                $result[1][] = "Password errata, se effettui troppi tentativi di accesso il tuo account potrebbe essere bloccato.";
                return $result;
            }
        }
    } else {
        // The user doesn't exist
        $result[1][] = "Utente o email errati.";
        return $result;
    }
}

/**
 * Logs out the user from the site.
 */
function logoutUser()
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

/**
 * Check if the user is logged in.
 * @param DatabaseHelper $dbh object that can communicate with the database.
 * @return bool true if the user is logged in, false otherwise.
 */
function login_check(DatabaseHelper $dbh)
{
    // Checking if all session's variables are correctly set
    if (isset($_SESSION['username'], $_SESSION['login_string'])) {
        $login_string = $_SESSION['login_string'];
        $username = $_SESSION['username'];
        $user_browser = $_SERVER['HTTP_USER_AGENT']; // gets user's 'user-agent' string.
        $password = $dbh->getPassword($username);

        if (count($password) == 1) { // the user exist
            $password = $password[0]["password"]; // retrieves the passwors
            $login_check = hash('sha512', $password . $user_browser);
            if ($login_check == $login_string) {
                // Login successful
                return true;
            } else {
                // Cookie login_check doesn't match
                return false;
            }
        } else {
            // The user doesn't exist or there are more than one
            return false;
        }
    } else {
        // Session cookie not set
        return false;
    }
}

/**
 * Checks if an account is being brute-forced.
 * @param string $username the account to be checked.
 * @param DatabaseHelper $dbh object that can communicate with the database.
 */
function checkBrute(string $username, DatabaseHelper $dbh)
{
    $now = time();
    $valid_attempts = $now - (2 * 60 * 60); // Since 2 hours ago
    $num_attempts = $dbh->getLoginAttempts($username, $valid_attempts);
    if (count($num_attempts) > MAX_LOGIN_ATTEMPTS) {
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
function registerAnimal(string $animal, string $type, array $file, string $description, array $owners, DatabaseHelper $dbh)
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

    if (!empty($dbh->getAnimalFromName($animal))) {
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
 * @param array $animal an associative array containing animal's info.
 * @param string $type the animal's type.
 * @param array $file an array containing the animal's image.
 * @param string $description the animal's description.
 * @param array $owners a list of the current animal's owners.
 * @param DatabaseHelper $dbh object to communicate with the database.
 * @return int 0 if there were errors, 1 otherwise.
 * @return array a string array of all the errors that occurred, it is empty if there were none.
 */
function editAnimal(array $animal, string $type, array $file, string $description, array $owners, DatabaseHelper $dbh)
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
function editOwnerships(array $owners, string $animal, DatabaseHelper $dbh)
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
function uploadImage(string $path, array $image)
{
    $imageName = basename($image["name"]);
    $fullPath = $path . $imageName;

    $maxKB = 500;
    $acceptedExtensions = array("jpg", "jpeg", "png", "gif");
    $result = 0;
    $msg = "";
    //Checking if $image is actually an image
    $imageSize = getimagesize($image["tmp_name"]);
    if ($imageSize === false) {
        $msg .= "File caricato non è un'immagine! ";
    }
    //Checking image extension
    $imageFileType = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
    if (!in_array($imageFileType, $acceptedExtensions)) {
        $msg .= "Accettate solo le seguenti estensioni: " . implode(",", $acceptedExtensions);
    }

    //If a file with the same name already exist the current one gets renamed
    if (file_exists($fullPath)) {
        $i = 1;
        do {
            $i++;
            $imageName = pathinfo(basename($image["name"]), PATHINFO_FILENAME) . "_$i." . $imageFileType;
        } while (file_exists($path . $imageName));
        $fullPath = $path . $imageName;
    }

    //If there are no errors the file gets compressed and moved to the destination folder 
    if (strlen($msg) == 0) {
        if (move_uploaded_file($image["tmp_name"], $fullPath)) {
            if (compressImage(BASE_FOLDER . $fullPath)) {
                $result = 1;
                $msg = $imageName;
            } else {
                $msg .= "Errore nel caricamento dell'immagine.";
            }
        }
    }
    return array($result, $msg);
}

/**
 * Compresses an image.
 * @param string $path full name of the image to be compressed.
 * @param bool true if the image was compressed successfully, false otherwise.
 */
function compressImage(string $path){
    $img = new Imagick();
    $img->readImage($path);
    $img->setImageCompression(Imagick::COMPRESSION_JPEG);
    $img->setImageCompressionQuality(75);
    $img->stripImage();
    $result = $img->writeImage();
    $img->clear();
    return $result;
}

/**
 * Checks if a password is strong enough.
 * @param string $password the password to be checked.
 * @return bool true if the password is strong enough, false otherwise.
 * @return array a string array of all the password's problems.
 */
function isPasswordStrong(string $password)
{
    $result = true;
    $errors = array();

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

/**
 * Checks the characters of a username.
 * @param string $username the username to be checked.
 * @return bool true if the username is acceptable, false otherwise.
 */
function isUserID(string $username)
{
    if (preg_match('/^[a-z\d_]{2,20}$/i', $username)) {
        return true;
    } else {
        return false;
    }
}

/**
 * Registers a new user into the database.
 * @param string $user the username.
 * @param string $email the user's email.
 * @param string $password the account's password.
 * @param string $confirm_password the password to check if it has been written correctly.
 * @param DatabaseHelper $dbh object to communicate with the database.
 * @return int 0 if there were errors, 1 otherwise.
 * @return array a string array of all the errors that occurred, it is empty if there were none.
 */
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
    if (login_check($dbh)) {
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
    //If errors occured the query stops
    if ($uploadErrors[0] == 1) {
        //There were no errors
        $imgUp = $uploadErrors[1]; //Gets the image full name, which may have changed
        $result = -1; //Not yet set
        if (strlen($alt) <= 50 && strlen($txt) <= 200) {
            $index = $dbh->addPost($imgUp, $alt, $txt, $user);
            if ($index != -1) {
                //Post has been added successfully
                if (!empty($pets)) {
                    foreach ($pets as $single) {
                        $tmp = $dbh->addAnimalToPost($index, $single);
                        if ($tmp == false) {
                            $result = 0;
                            //Couldn't add animal to post
                            $errors = "C'è stato un errore nell'esecuzione della query sul database";
                        }
                    }
                    if ($result == -1) {
                        //No errors
                        $result = 1;
                    }
                } else {
                    //There are no animals to be added
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
        //An error during the upload has occured
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
        // Returning directly the first associative array because there can be only one user associated to a specific username 
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
        // Returning directly the first associative array because there can be only one animal associated to a specific username 
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

/**
 * Changes the password of a user.
 * @param string $oldPassword the password that is currently used for this account.
 * @param string $newPassword the new password to be used.
 * @param string $confirmPassword password to check if the new password has been written correctly.
 * @param DatabaseHelper $dbh object to communicate with the database.
 * @return bool true if the password was changed successfully, false otherwise.
 * @return array a string array ofthe errors that occured, empty if none occured.
 */
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
        $user = $dbh->getUserFromName(getUserName($dbh));
        if (password_verify($oldPassword, $user[0]['password'])) {
            $result = $dbh->changePassword(getUserName($dbh), $newPassword);
            //Notify user about password change
            if (isNotificationPasswordChangeEnabled(getUserName($dbh), $dbh)) {
                sendEmailAboutPasswordChange(getUserName($dbh), $dbh);
            }
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
        // Returning directly the first associative array because there can be only one post associated to a specific id 
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
 * Creates the href for a person profile page
 * @param string $username the person's username
 * @return string the href
 */
function getUserProfileHref(string $username)
{
    return "view-user-profile.php?username=" . $username . "&type=person";
}

/**
 * Creates the href for an animal profile page
 * @param string $username the animal's username
 * @return string the href
 */
function getAnimalProfileHref(string $username)
{
    return "view-user-profile.php?username=" . $username . "&type=animal";
}

/**
 * Returns user profile href reference 
 * @param string $username the user's username
 * @param string $type defines if the user is an animal or a person
 * @return string the href
 */
function getProfileHref(string $username, string $type)
{
    return "view-user-profile.php?username=" . $username . "&type=" . $type;
}

/**
 * Creates the href for a post page
 * @param int $id the post's id
 * @return string the href
 */
function getPostHref(int $id)
{
    return "view-post-profile.php?id=" . $id;
}
/**
 * Returns the relative path for the user's profile image
 * @param string $user user's username
 * @return string the relative path
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
 * Returns the n more recent post's comments
 * @param int $id_post the post's id
 * @param int $n number of comments to be loaded
 * @param DatabaseHelper $dbh object to communicate with the database.
 * @return array the array containing all the comments
 */
function loadMostRecentComments(int $id_post, int $n, DatabaseHelper $dbh)
{
    return $dbh->getMostRecentComments($id_post, $n);
}

/**
 * Checks if a comments has been answered.
 * @param int $id_comment the id of the comment to be checked.
 * @param DatabaseHelper $dbh object to communicate with the database.
 * @return true if the comment has been answered, false otherwise.
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
 * Gets the comments info.
 * @param int $id the comment's id.
 * @param DatabaseHelper $dbh object to communicate with the database.
 * @return array an associative array containing the infos of the comment.
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
 * Inserts a new comment.
 * @param string $username the username of the user that made the comment.
 * @param string $text the comment's text.
 * @param int $id_post the post where the comment was made.
 * @param DatabaseHelper $dbh object to communicate with the database.
 * @return int the comment id or -1 if insertion went wrong
 */
function newComment(string $username, string $text, int $id_post, DatabaseHelper $dbh)
{
    return $dbh->addNewComment($username, $text, $id_post);
}

/**
 * Inserts an answer to a comment.
 * @param string $username the username of the user that made the comment.
 * @param string $text the comment's text.
 * @param int $id_post the post where the comment was made.
 * @param int $id_padre the id of the answered comment.
 * @param DatabaseHelper $dbh object to communicate with the database.
 * @return int the comment id or -1 if insertion went wrong
 */
function newCommentAnswer(string $username, int $id_padre, string $text, int $id_post, DatabaseHelper $dbh)
{
    return $dbh->addNewCommentToComment($username, $id_padre, $text, $id_post);
}

/**
 * Returns n post's comments which are older than the specified timestamp. 
 * @param int $id the post's id.
 * @param int $n number of comments to be loaded.
 * @param int $offset the offset.
 * @param string $timestamp time limit.
 * @param DatabaseHelper $dbh object to communicate with the database.
 * @return array an array containing the loaded comments.
 */
function getRecentComments(int $id, int $n, int $offset, string $timestamp, DatabaseHelper $dbh)
{
    return $dbh->getCommentOffset($id, $n, $offset, $timestamp);
}

/**
 * Returns n comment's answers which are older than the specified timestamp. 
 * @param int $id the post's id.
 * @param int $id_comment the comment's id.
 * @param int $n number of answers to be loaded.
 * @param int $offset the offset.
 * @param string $timestamp time limit.
 * @param DatabaseHelper $dbh object to communicate with the database.
 * @return array an array containing the loaded answers.
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
 * Counts how many files with the specified extension are in a directory.
 * @param string $directory the directory.
 * @param string $extension the specified extension.
 * @return int number of accepted files.
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

/**
 * Creates a string which identifies the password reset.
 * @param string $email the email of the account.
 * @param DatabaseHelper $dbh object to communicate with the database.
 * @return string reset code.
 */
function createResetCode(string $email, DatabaseHelper $dbh){
    $length=50; //Ogni byte è 2 caratteri, la stringa è lunga 100 char in db
    $code=bin2hex(random_bytes($length));
    $dbh->newResetCode($email, $code);
    return $code;
}

/**
 * Sends an email to reset the password
 * @param string $email email address where the email must be sent.
 * @param string $code the password reset code.
 * @return bool true if the email was sent successfully, false otherwise.
 */
function sendResetEmail(string $email,string $code){

    // The message
    $url="http://".$_SERVER["HTTP_HOST"]."/new-password-reset.php?id=".$code;
    $urlReset="http://".$_SERVER["HTTP_HOST"]."/reset-password.php";
    $message = "Hai chiesto il reset della tua password su TWPETS?\nSe sei stato tu, clicca sul link in fondo a questa mail o copialo per intero su un browser per procedere con il reset della tua password\nSe non sei stato tu a , ignora questa email\n";
    $message = $message."Premi qui per resettare la password: ".$url."\n";
    $message= $message. "Il link è valido per 24h, se è passato più tempo, torna su ".$urlReset." a richiedere l'invio di un nuovo codice.\n";

    $headers = 'From: noreply@twpets.com' . "\r\n";
    // Sending the email
    return mail($email, "TWPETS - Richiesta di reset password", $message, $headers);
}

/**
 * Checks if a reset code is valid.
 * @param string $code the password reset code.
 * @param DatabaseHelper $dbh object to communicate with the database.
 * @return bool true if the reset code is valid, false otherwise.
 */
function isPasswordResetCodeValid(string $code, DatabaseHelper $dbh){
    $result=$dbh->getResetCodeInfo($code);
    if(empty($result)==false){
        $time=$result["generated_on"];
        $email=$result["email"];
        $now=date('Y-m-d H:i:s', time());
        $interval=date_diff(date_create($time),date_create($now));
        $validityH=24; //The reset code is valid only for 24 hours
        if((($interval->days*24)+$interval->h)>$validityH){
            return false;
        }else{
            //The reset code has been generated less than 24 hours ago
            $allCodes=$dbh->getAllResetCodesForEmail($email);
            if(count($allCodes)>=1&&$allCodes[0]["generated_key"]==$code){
                // $allCodes is ordered from the most recent, so only the latest is checked
                return true;
            }
            return false;
        }
    }
    return false;
}

/**
 * Changes the password of the user who asked for a password reset.
 * @param string $username the user's username.
 * @param string $newPassword the new password.
 * @param string $confirmPassword the password to check if it has been written correctly.
 * @param DatabaseHelper $dbh object to communicate with the database.
 * @return bool true if the password was changed successfully, false otherwise.
 * @return array a string array containing all of the errors that occurred.
 */
function changePasswordReset(string $username, string $newPassword, string $confirmPassword, DatabaseHelper $dbh)
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
        $result = $dbh->changePassword($username, $newPassword);
        //Notify user about password change
        if (isNotificationPasswordChangeEnabled(getUserName($dbh), $dbh)) {
            sendEmailAboutPasswordChange(getUserName($dbh), $dbh);
        }
    }
    return array($result, $errors);
}

/**
 * Removes all the password reset codes from an account
 * @param string $email the account's email
 * @param DatabaseHelper $dbh object to communicate with the database.
 * @return bool true if all the codes were deleted successfully, false otherwise.
 */
function removeAllPasswordChangeRequests(string $email,DatabaseHelper $dbh){
    return $dbh->removeAllPasswordCodes($email);
}

/**
 * Sends an email to the user's email address to notify a password change.
 * @param string $username the user's username
 * @param DatabaseHelper $dbh object to communicate with the database.
 * @return bool true if the mail was sent successfully, false otherwise.
 */
function sendEmailAboutPasswordChange(string $username, DatabaseHelper $dbh){
    $email=getUserData($username, $dbh)["email"];
    // The message
    $message = "La tua password su TWPETS è stata cambiata\nSe sei stato tu, ignora questa email.\nSe non sei stato tu, il tuo account è compromesso ed è necessario che esegui la procedura di reset della password al più presto";
    $headers = 'From: noreply@twpets.com' . "\r\n";
    // Sending the email
    return mail($email, "TWPETS - Password cambiata", $message, $headers);
}

/**
 * Sends an email to the user's email address to notify that the account has been disabled
 * @param string $email the user's email
 * @return bool true if the mail was sent successfully, false otherwise.
 */
function sendEmailAboutDisabledAccount(string $email){
    // The message
    $message = "Il tuo account su TWPETS è stato disabilitato\nQuesto accade quando ci sono troppi tentativi di login con password errata in breve tempo.\nPer riabilitare il tuo account, esegui la procedura di reset della password al più presto";
    $headers = 'From: noreply@twpets.com' . "\r\n";
    // Sending the email
    return mail($email, "TWPETS - Account disabilitato", $message, $headers);
    
}
