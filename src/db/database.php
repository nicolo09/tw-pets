<?php
class DatabaseHelper
{
    private mysqli $db;

    /**
     * Createas a DatabaseHelper object.
     * @param string $servername the name of the server.
     * @param string $username the username to access the database.
     * @param string $password the password to access the database.
     * @param string $dbaname the name of the database.
     * @param int $port the port used to connect to the server.
     */
    public function __construct(string $servername, string $username, string $password, string $dbname, int $port)
    {
        $this->db = new mysqli($servername, $username, $password, $dbname, $port);
        if ($this->db->connect_error) {
            die("Connection failed: " . $this->db->connect_error);
        }
    }

    /**
     * Returns a user knowing its email.
     * @param string $email the email address of the account.
     * @return array an array of associative arrays containing the user.
     */
    public function getUser(string $email)
    {
        // Preventing SQL injection attacks by using sql prepared statement.
        if ($stmt = $this->db->prepare("SELECT username, password FROM PERSONA WHERE email = ? LIMIT 1")) {
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        }
    }

    /**
     * Gets all the users who are following and are being followed by a given user.
     * @param string $username the user' username.
     * @return array an array of associative arrays containing all the mutual followers.
     */
    public function getMutualFollowers(string $username)
    {
        $query = "SELECT p.username, p.immagine
        FROM PERSONA p
        INNER JOIN SEGUE_PERSONA sp1 ON p.username = sp1.followed
        INNER JOIN SEGUE_PERSONA sp2 ON p.username = sp2.follower
        WHERE sp1.follower = ? AND sp2.followed = ? ORDER BY p.username ASC";

        if ($stmt = $this->db->prepare($query)) {
            $stmt->bind_param('ss', $username, $username);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }
    }

    /**
     * Gets some users whose username is similar to the given one, 
     * ordered by their popularity and then alphabetically.
     * @param string $username the username to use as reference.
     * @param int $offset how many results should be ignored starting from the top.
     * @param int $limit how many results the function should return.
     * @return array an array of associative arrays.
     */
    public function getPersonsLike(string $username, int $offset, int $limit)
    {
        $value = "%" . $username . "%";

        $query = "SELECT p.username, p.immagine
        FROM PERSONA p
        LEFT JOIN SEGUE_PERSONA sp ON p.username = sp.followed
        WHERE p.username LIKE ?
        GROUP BY p.username
        ORDER BY COUNT(sp.follower) DESC, p.username 
        LIMIT ?, ?";

        if ($stmt = $this->db->prepare($query)) {
            $stmt->bind_param('sii', $value, $offset, $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }
    }

    /**
     * Gets some animals whose username is similar to the given one, 
     * ordered by their popularity and then alphabetically.
     * @param string $username the username to use as reference.
     * @param int $offset how many results should be ignored starting from the top.
     * @param int $limit how many results the function should return.
     * @return array an array of associative arrays.
     */
    public function getAnimalsLike(string $username, int $offset, int $limit)
    {
        $value = "%" . $username . "%";

        $query = "SELECT a.username, a.immagine
        FROM ANIMALE a
        LEFT JOIN SEGUE_ANIMALE sa ON a.username = sa.followed
        WHERE a.username LIKE ?
        GROUP BY a.username
        ORDER BY COUNT(sa.follower) DESC, a.username
        LIMIT ?, ?";

        if ($stmt = $this->db->prepare($query)) {
            $stmt->bind_param('sii', $value, $offset, $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }
    }

    /**
     * Gets some followers of an user ordered by their popularity.
     * @param string $username the user's username.
     * @param int $offset how many results should be ignored starting from the top.
     * @param int $limit how many results the function should return.
     * @return array an array of associative arrays.
     */
    public function getPersonFollowers(string $username, int $offset, int $limit)
    {

        $query = "SELECT P.username, P.immagine
        FROM PERSONA P
        LEFT JOIN SEGUE_PERSONA SP ON P.username = SP.followed
        JOIN (
            SELECT P.username
            FROM PERSONA P
            JOIN SEGUE_PERSONA SP ON P.username = SP.follower
            WHERE SP.followed = ?
        ) AS subquery ON P.username = subquery.username
        GROUP BY P.username
        ORDER BY COUNT(SP.follower) DESC, P.username
        LIMIT ?, ?";

        if ($stmt = $this->db->prepare($query)) {
            $stmt->bind_param('sii', $username, $offset, $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }
    }

    /**
     * Gets some followers of an animal ordered by their popularity.
     * @param string $username the animal's username.
     * @param int $offset how many results should be ignored starting from the top.
     * @param int $limit how many results the function should return.
     * @return array an array of associative arrays.
     */
    public function getAnimalFollowers(string $username, int $offset, int $limit)
    {

        $query = "SELECT P.username, P.immagine
        FROM PERSONA P
        LEFT JOIN SEGUE_PERSONA SP ON P.username = SP.followed
        JOIN (
            SELECT P.username
            FROM PERSONA P
            JOIN SEGUE_ANIMALE SA ON P.username = SA.follower
            LEFT JOIN POSSIEDE PO ON PO.persona = SA.follower AND PO.animale = ?
            WHERE SA.followed = ? AND PO.animale IS NULL
        ) AS subquery ON P.username = subquery.username
        GROUP BY P.username
        ORDER BY COUNT(SP.follower) DESC, P.username
        LIMIT ?, ?";

        if ($stmt = $this->db->prepare($query)) {
            $stmt->bind_param('ssii', $username, $username, $offset, $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }
    }

    /**
     * Registers a new animal.
     * @param string $username the animal's username.
     * @param string $type what type of animal it is.
     * @param string $img the animal's profile picture image name.
     * @param string $description the animal profile's description.
     * @return bool true if the profile was created successfully, false otherwise.
     */
    public function addAnimal(string $username, string $type, string $img, string $description)
    {
        if ($stmt = $this->db->prepare("INSERT INTO ANIMALE (username, tipo, immagine, descrizione) VALUES (?, ?, ?, ?)")) {
            $stmt->bind_param('ssss', $username, $type, $img, $description);
            return $stmt->execute();
        } else {
            return false;
        }
    }

    /**
     * Updates the animal's profile.
     * @param string $username the animal's username.
     * @param string $type what type of animal it is.
     * @param string $img the animal's profile picture image name.
     * @param string $description the animal profile's description.
     * @return bool true if the profile was updated successfully, false otherwise.
     */
    public function updateAnimal(string $username, string $type, string $img, string $description)
    {
        if ($stmt = $this->db->prepare("UPDATE ANIMALE SET tipo = ?, immagine = ?, descrizione = ? WHERE username = ?")) {
            $stmt->bind_param("ssss", $type, $img, $description, $username);
            return $stmt->execute();
        } else {
            return false;
        }
    }

    /**
     * Updates the user's profile.
     * @param string $username the user's username.
     * @param string $employment what the user's does.
     * @param string $img the user's profile picture image name.
     * @param string $description the user profile's description.
     * @return bool true if the profile has been updated successfully, false otherwise.
     */
    public function updateUserProfile(string $username, string $employment, string $img, string $description)
    {
        if ($stmt = $this->db->prepare("UPDATE PERSONA SET impiego = ?, immagine = ?, descrizione = ? WHERE username = ?")) {
            $stmt->bind_param("ssss", $employment, $img, $description, $username);
            return $stmt->execute();
        } else {
            return false;
        }
    }

    /**
     * Gets the animal from its username.
     * @param string $animal the animal username.
     * @return array an associative array.
     */
    public function getAnimalFromName(string $animal)
    {
        if ($stmt = $this->db->prepare("SELECT * FROM ANIMALE WHERE username = ?")) {
            $stmt->bind_param('s', $animal);
            $stmt->execute();
            $result = $stmt->get_result();
            $tmp = $result->fetch_all(MYSQLI_ASSOC);
            if (empty($tmp) == false) {
                return $tmp[0];
            } else {
                return array();
            }
        }
        return array();
    }

    /**
     * Registers an ownership relation between a user and an animal.
     * @param string $owner the user.
     * @param string $animal the animal.
     * @return bool true if the ownership was successfully registered, false otherwise or if the user doesn't exist.
     */
    public function registerOwnership(string $owner, string $animal)
    {
        if (count($this->getUserFromName($owner)) == 1) {
            if ($stmt = $this->db->prepare("INSERT INTO POSSIEDE (persona, animale) VALUES (?, ?)")) {
                $stmt->bind_param('ss', $owner, $animal);
                return $stmt->execute();
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Removes the ownership of an animal from a user.
     * @param string $owner the user.
     * @param string $animal the animal.
     * @return bool true if the ownership was successfully removed, false otherwise.
     */
    public function deleteOwnership(string $owner, string $animal)
    {
        if ($stmt = $this->db->prepare("DELETE FROM POSSIEDE WHERE persona = ? AND animale = ?")) {
            $stmt->bind_param('ss', $owner, $animal);
            return $stmt->execute();
        } else {
            return false;
        }
    }

    /**
     * Gets all the owners of an animal ordered by their popularity.
     * @param string $animal the animal.
     * @return array an array of associative arrays of the owners of the specified animal.
     */
    public function getOwners(string $animal)
    {

        $query = "SELECT P.username, P.immagine
        FROM PERSONA P
        LEFT JOIN SEGUE_PERSONA SP ON P.username = SP.followed
        JOIN (
            SELECT p.username
            FROM PERSONA p
            JOIN POSSIEDE po ON p.username = po.persona
            WHERE po.animale = ?
        ) AS subquery ON P.username = subquery.username
        GROUP BY P.username
        ORDER BY COUNT(SP.follower) DESC, P.username";

        if ($stmt = $this->db->prepare($query)) {
            $stmt->bind_param('s', $animal);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }
    }

    /**
     * Checks if a user owns the specified animal.
     * @param string $owner the possible owner.
     * @param string $animal the animal to check.
     * @return bool true if the user owns the animal, false otherwise.
     */
    public function checkOwnership(string $owner, string $animal)
    {
        if ($stmt = $this->db->prepare("SELECT * FROM POSSIEDE WHERE persona = ? AND animale = ?")) {
            $stmt->bind_param('ss', $owner, $animal);
            $stmt->execute();
            $result = $stmt->get_result();
            return count($result->fetch_all(MYSQLI_ASSOC)) == 1;
        } else {
            return false;
        }
    }

    /**
     * Returns the password of a user.
     * @param string $username the user's username.
     * @return array an array containing an associative array where the password can be found.
     */
    public function getPassword(string $username)
    {
        if ($stmt = $this->db->prepare("SELECT password FROM PERSONA WHERE username = ? LIMIT 1")) {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        }
    }

    /**
     * Registers a failed login attempt.
     * @param string $username the user's username.
     */
    public function addLoginAttempt(string $username)
    {
        if ($stmt = $this->db->prepare("INSERT INTO TENTATIVO_LOGIN (timestamp, username) VALUES (NOW(), ?)")) {
            $stmt->bind_param('s', $username);
            $stmt->execute();
        }
    }

    /**
     * Gets all the login attempts more recent than a specified time.
     * @param string $username the user's username.
     * @param string $from timestamp.
     * @return array an array of associative arrays containing the login attempts.
     */
    public function getLoginAttempts(string $username, string $from)
    {
        $stmt = $this->db->prepare("DELETE FROM TENTATIVO_LOGIN WHERE username = ? AND timestamp < FROM_UNIXTIME($from)");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        if ($stmt = $this->db->prepare("SELECT timestamp FROM TENTATIVO_LOGIN WHERE username = ? AND timestamp > FROM_UNIXTIME($from)")) {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        }
    }

    /**
     * Gets all of the parameters of an account from the username.
     * @param string $username the user's username.
     * @return array an array of associative arrays containg the account parameters.
     */
    public function getUserFromName($username)
    {
        if ($stmt = $this->db->prepare("SELECT * FROM PERSONA WHERE username = ? LIMIT 1")) {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        }
    }

    /**
     * Adds a new user to the database.
     * @param string $user the user's username.
     * @param string $password the user's password.
     * @param string $email the user's email address.
     * @return bool true if the user was added successfully, false otherwise.
     */
    public function addUser(string $user, string $password, string $email)
    {
        if ($stmt = $this->db->prepare("INSERT INTO PERSONA (username, password, email) VALUES (?, ?, ?)")) {
            $password = password_hash($password, PASSWORD_DEFAULT);
            $stmt->bind_param('sss', $user, $password, $email);
            if ($stmt->execute()) {
                $stmt = $this->db->prepare("INSERT INTO IMPOSTAZIONE(username) VALUES (?)");
                $stmt->bind_param('s', $user);
                return $stmt->execute();
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Updates an user's settings.
     * @param string $username the user's username.
     * @param string $setting the setting that must be modified.
     * @param string $value defines if the setting must be enabled or disabled.
     * @return bool true if the setting was updated successfully, false otherwise.
     */
    public function updateSetting(string $username, string $setting, string $value)
    {
        if ($stmt = $this->db->prepare("UPDATE IMPOSTAZIONE SET `$setting` = ? WHERE username = ?")) {
            $value = $value == "true" ? 1 : 0;
            $stmt->bind_param('is', $value, $username);

            return $stmt->execute();
        } else {
            return false;
        }
    }

    /**
     * Gets the settings' current status of a user.
     * @param string $username the user's username.
     * @param array an array of associative arrays containing the settings status.
     */
    public function getSettings(string $username)
    {
        if ($stmt = $this->db->prepare("SELECT * FROM IMPOSTAZIONE WHERE username = ? LIMIT 1")) {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        }
    }

    /**
     * Changhes the email associated to an account.
     * @param string $oldEmail the previous email address.
     * @param string $newEmail the new email address.
     * @return bool true if the email was changed successfully, false otherwise.
     */
    public function changeEmail(string $oldEmail, string $newEmail)
    {
        if ($stmt = $this->db->prepare("UPDATE PERSONA SET email = ? WHERE email = ?")) {
            $stmt->bind_param('ss', $newEmail, $oldEmail);
            return $stmt->execute();
        } else {
            return false;
        }
    }

    /**
     * Creates a new post
     * @param string $img the file of the image of the new post
     * @param string $alt the description of the image
     * @param string $txt the description of the new post
     * @param string $user the username of the user creating the post
     * @return int of created post or -1 if creation went wrong
     */
    /*This function returns the id of the created post or -1 if something went wrong*/
    public function addPost(string $img, string $alt, string $txt, string $user)
    {
        if ($stmt = $this->db->prepare("INSERT INTO POST (immagine, alt, username, testo) VALUES (?, ?, ?, ?)")) {
            $stmt->bind_param('ssss', $img, $alt, $user, $txt);
            if ($stmt->execute() == true) {
                return $this->db->insert_id;
            }
        }
        return -1;
    }

    /**
     * Returns the managed animals of a user
     * @param string $user the username of the user
     * @return array of managed animals
     */
    public function getOwnedAnimals(string $user)
    {
        if ($stmt = $this->db->prepare("SELECT * FROM POSSIEDE JOIN ANIMALE ON ANIMALE.username=POSSIEDE.animale WHERE persona = ?")) {
            $stmt->bind_param('s', $user);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }
    }

    /**
     * Adds an animal user to a post
     * @param int $idPost the post id
     * @param string $username the username of the animal
     * @return bool false if insertion went wrong
     */
    public function addAnimalToPost(int $idPost, string $username)
    {
        if ($stmt = $this->db->prepare("INSERT INTO RIGUARDA (id_post, animale) VALUES (?,?)")) {
            $stmt->bind_param('is', $idPost, $username);
            return $stmt->execute();
        } else {
            return false;
        }
    }

    /**
     * Returns user information
     * @param string $user the username of the user
     * @return array of array of user information
     */
    public function getUserInfo(string $username)
    {
        if ($stmt = $this->db->prepare("SELECT username, descrizione, immagine, impiego FROM PERSONA WHERE username = ?")) {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }
    }

    /**
     * Returns all posts made by a user
     * @param string $username the username of the user
     * @return array of posts
     */
    public function getUserPosts(string $username)
    {
        if ($stmt = $this->db->prepare("SELECT * FROM POST WHERE username = ? ORDER BY POST.timestamp DESC")) {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }
    }

    /**
     * Checks if a person username exists
     * @param string $username the username of the person
     * @return array that is empty if user doesn't exist
     */
    public function doesUserExist(string $username)
    {
        if ($stmt = $this->db->prepare("SELECT COUNT(username) FROM PERSONA WHERE username=?")) {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }
    }

    /**
     * Returns all the followers of a given user
     * @param string $username the username of the person
     * @return array of followers
     */
    public function getAllFollowers(string $username)
    {
        if ($stmt = $this->db->prepare("SELECT followers FROM SEGUE_PERSONA WHERE followed=?")) {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }
    }

    /**
     * Checks if a user follows another
     * @param string $followedUsername the username of followed
     * @param string $followerUsername the username of the follower
     * @return int 1 if follower follows followed
     */
    public function doesUserFollowMyAccount(string $followedUsername, string $followerUsername)
    {
        if ($stmt = $this->db->prepare("SELECT * FROM SEGUE_PERSONA WHERE followed=? AND follower=?")) {
            $stmt->bind_param('ss', $followedUsername, $followerUsername);
            $stmt->execute();
            $result = $stmt->get_result();
            $tmp = $result->fetch_all(MYSQLI_ASSOC);
            if (count($tmp) == 1) {
                return 1;
            }
            return 0;
        } else {
            return 0;
        }
    }
    /**
     * Checks if a animal username exists
     * @param string $username the username of the animal
     * @return array empty if username doesn't exist
     */
    public function doesAnimalExist(string $username)
    {
        if ($stmt = $this->db->prepare("SELECT COUNT(username) FROM ANIMALE WHERE username=?")) {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }
    }

    /**
     * Returns animal information
     * @param string $username the username of the animal
     * @return array of animal information
     */
    public function getAnimalInfo(string $username)
    {
        if ($stmt = $this->db->prepare("SELECT username, descrizione, immagine, tipo FROM ANIMALE WHERE username = ?")) {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }
    }

    /**
     * Returns all posts of an animal
     * @param string $username the username of the animal
     * @return array of posts
     */
    public function getAnimalPosts(string $username)
    {
        if ($stmt = $this->db->prepare("SELECT * FROM RIGUARDA JOIN POST ON RIGUARDA.id_post=POST.id_post WHERE RIGUARDA.animale=?")) {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }
    }

    /**
     * Checks if a user follows an animal
     * @param string $username the username of user
     * @param string $animal the username of the animal being followed
     * @return array that is empty if user doesn't follow animal
     */
    public function doesUserFollowAnimal(string $username, string $animal)
    {
        if ($stmt = $this->db->prepare("SELECT * FROM SEGUE_ANIMALE WHERE follower=? AND followed=?")) {
            $stmt->bind_param('ss', $username, $animal);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }
    }

    /**
     * Makes a person follow another
     * @param string $followed the username of followed
     * @param string $follower the username of the follower
     * @return bool false if something went wrong
     */
    public function addFollowPerson(string $followed, string $follower)
    {
        if ($stmt = $this->db->prepare("INSERT INTO SEGUE_PERSONA (followed, follower) VALUES (?,?)")) {
            $stmt->bind_param('ss', $followed, $follower);
            return $stmt->execute();
        } else {
            return false;
        }
    }

    /**
     * Makes a person unfollow another
     * @param string $followed the username of followed
     * @param string $follower the username of the follower
     * @return bool false if something went wrong
     */
    public function removeFollowPerson(string $followed, string $follower)
    {
        if ($stmt = $this->db->prepare("DELETE FROM SEGUE_PERSONA WHERE followed=? AND follower=?")) {
            $stmt->bind_param('ss', $followed, $follower);
            return $stmt->execute();
        } else {
            return false;
        }
    }

    /**
     * Makes a person follow an animal
     * @param string $animal the username of animal
     * @param string $follower the username of the follower
     * @return bool false if something went wrong
     */
    public function addFollowAnimal(string $animal, string $follower)
    {
        if ($stmt = $this->db->prepare("INSERT INTO SEGUE_ANIMALE (followed, follower) VALUES (?,?)")) {
            $stmt->bind_param('ss', $animal, $follower);
            return $stmt->execute();
        } else {
            return false;
        }
    }
    /**
     * Makes a person unfollow an animal
     * @param string $animal the username of animal
     * @param string $follower the username of the follower
     * @return bool false if something went wrong
     */
    public function removeFollowAnimal(string $animal, string $follower)
    {
        if ($stmt = $this->db->prepare("DELETE FROM SEGUE_ANIMALE WHERE followed=? AND follower=?")) {
            $stmt->bind_param('ss', $animal, $follower);
            return $stmt->execute();
        } else {
            return false;
        }
    }

    /**
     * Changes a user's account password.
     * @param string $username the user's username.
     * @param string $newPassword the new password to be set.
     * @return bool true if the password was changed successfully, false otherwise.
     */
    public function changePassword(string $username, string $newPassword)
    {
        if ($stmt = $this->db->prepare("UPDATE PERSONA SET password = ? WHERE username = ?")) {
            $newPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt->bind_param('ss', $newPassword, $username);
            return $stmt->execute();
        } else {
            return false;
        }
    }

    /**
     * Returns all info about a post
     * @param int $id the post id
     * @return array of array of post info
     */
    public function getPostInfo(int $id)
    {
        if ($stmt = $this->db->prepare("SELECT POST.id_post, POST.immagine, POST.alt, POST.testo, POST.timestamp, PERSONA.username, PERSONA.immagine as immagineprofilo FROM POST JOIN PERSONA ON POST.username=PERSONA.username WHERE POST.id_post=?")) {
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }
    }

    /**
     * Returns number of likes of a post
     * @param int $id the post id
     * @return array with number of likes
     */
    public function getPostLikes($id)
    {
        if ($stmt = $this->db->prepare("SELECT COUNT(*) FROM LIKES WHERE id_post=?")) {
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }
    }

    /**
     * Returns if a user has liked a post
     * @param int $id the post id
     * @param string $username the user
     * @return array empty if user hasn't liked the post
     */
    public function doesUserLikePost(int $id, string $username)
    {
        if ($stmt = $this->db->prepare("SELECT COUNT(*) FROM `LIKES` WHERE username=? AND id_post=?")) {
            $stmt->bind_param('si', $username, $id);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }
    }

    /**
     * Returns if a user has saved a post
     * @param int $id the post id
     * @param string $username the user
     * @return array empty if user hasn't saved the post
     */
    public function hasUserSavedPost(int $id, string $username)
    {
        if ($stmt = $this->db->prepare("SELECT COUNT(*) FROM SALVATI WHERE username=? AND id_post=?")) {
            $stmt->bind_param('si', $username, $id);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }
    }

    /**
     * Returns if a post id exists
     * @param int $id the post id
     * @return array empty if post doesn't exist
     */
    public function isIdPostCorrect(int $id)
    {
        if ($stmt = $this->db->prepare("SELECT COUNT(id_post) FROM POST WHERE id_post=?")) {
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }
    }

    /**
     * Makes a person like a post
     * @param int $id the post id
     * @param string $username the username of the user
     * @return bool false if something went wrong
     */
    public function addLikePost(int $id, string $username)
    {
        if ($stmt = $this->db->prepare("INSERT INTO LIKES (id_post, username) VALUES (?,?)")) {
            $stmt->bind_param('is', $id, $username);
            return $stmt->execute();
        } else {
            return false;
        }
    }

    /**
     * Makes a person unlike a post
     * @param int $id the post id
     * @param string $username the username of the user
     * @return bool false if something went wrong
     */
    public function removeLikePost(int $id, string $username)
    {
        if ($stmt = $this->db->prepare("DELETE FROM LIKES WHERE id_post=? AND username=?")) {
            $stmt->bind_param('is', $id, $username);
            return $stmt->execute();
        } else {
            return false;
        }
    }
    /**
     * Makes a person save a post
     * @param int $id the post id
     * @param string $username the username of the user
     * @return bool false if something went wrong
     */
    public function addSavePost(int $id, string $username)
    {
        if ($stmt = $this->db->prepare("INSERT INTO SALVATI (id_post, username) VALUES (?,?)")) {
            $stmt->bind_param('is', $id, $username);
            return $stmt->execute();
        } else {
            return false;
        }
    }

    /**
     * Makes a person unsave a post
     * @param int $id the post id
     * @param string $username the username of the user
     * @return bool false if something went wrong
     */
    public function removeSavePost(int $id, string $username)
    {
        if ($stmt = $this->db->prepare("DELETE FROM SALVATI WHERE id_post=? AND username=?")) {
            $stmt->bind_param('is', $id, $username);
            return $stmt->execute();
        } else {
            return false;
        }
    }

    /**
     * Returns all animals in a post
     * @param int $post the post id
     * @return array of array of animals
     */
    public function getTaggedAnimals(int $post)
    {
        if ($stmt = $this->db->prepare("SELECT animale FROM RIGUARDA WHERE id_post=?")) {
            $stmt->bind_param('i', $post);

            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }
    }

    /**
     * Gets a user's saved posts.
     * @param string $username the user's username.
     * @param int $from the posts must be more recent than the $from timestamp.
     * @param int $offset the offset.
     * @param int $n number of saved posts to get.
     * @return array an associative array containing the user's saved post.
     */
    public function getFavoritePosts(string $username, int $from, int $offset, int $n)
    {
        if ($stmt = $this->db->prepare("SELECT * FROM SALVATI JOIN POST ON SALVATI.id_post=POST.id_post WHERE SALVATI.username=? AND POST.timestamp>? ORDER BY timestamp DESC LIMIT ?,?")) {
            $stmt->bind_param('siii', $username, $from, $offset, $n);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        throw new Exception("Errore nella query");
    }



    /**
     * Returns the n most recent comments of a post.
     * @param int $id_post the post's id.
     * @param int $n number of comments to load.
     * @return array array of associative arrays containing the comments.
     */
    public function getMostRecentComments(int $id_post, int $n)
    {
        if ($stmt = $this->db->prepare("SELECT * FROM `COMMENTO` WHERE id_padre IS NULL AND id_post=? ORDER BY timestamp DESC LIMIT ?")) {
            $stmt->bind_param('ii', $id_post, $n);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }
    }

    /**
     * Checks if a comments has been answered.
     * @param int $id_comment the comment's id.
     * @return array an array of associative arrays containing how many answers a comment has.
     */
    public function doesCommentHaveAnswers(int $id_comment)
    {
        if ($stmt = $this->db->prepare("SELECT COUNT(*) FROM `COMMENTO` WHERE id_padre=? ORDER BY timestamp DESC")) {
            $stmt->bind_param('i', $id_comment);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }
    }
    /**
     * Checks if a user has more than x notifications.
     * @param string $username the user's username.
     * @param int $x the quantity of notifications to check.
     * @return bool true if the user has more than x notifications, false otherwise.
     * @throws Excepetion if couldn't process the request.
     */
    public function hasMoreThanXNotifications(string $username, int $x)
    {
        if ($stmt = $this->db->prepare("SELECT COUNT(*)>? FROM NOTIFICA WHERE destinatario = ?")) {
            $stmt->bind_param('is', $x, $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $tmp = $result->fetch_all(MYSQLI_NUM);
            if ($tmp[0][0] == 1) {
                return true;
            } else if ($tmp[0][0] == 0) {
                return false;
            }
        }
        throw new Exception("Error Processing Request", 1);
    }

    /**
     * Returns the number of notifications of the user.
     * @param string $username the user's uusername.
     * @return int number of notifications.
     * @throws Exception if couldn't process the request.
     */
    public function getNumberOfNotifications(string $username): int
    {
        if ($stmt = $this->db->prepare("SELECT COUNT(*) FROM NOTIFICA WHERE destinatario = ?")) {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $tmp = $result->fetch_all(MYSQLI_NUM);
            return $tmp[0][0];
        }
        throw new Exception("Error Processing Request", 1);
    }

    /**
     * Returns the first n notifications of the user ordered by timestamp with offset o.
     * @param string $username the user's username.
     * @param int $n how many notifications to return.
     * @param int $o offset.
     * @return array an array of associative arrays containing the notifications.
     * @throws Exception if couldn't process the request.
     */
    public function getNotifications(string $username, int $n, int $o): array
    {
        if ($stmt = $this->db->prepare("SELECT * FROM NOTIFICA WHERE destinatario = ? ORDER BY timestamp DESC LIMIT $o, $n")) {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        throw new Exception("Error Processing Request", 1);
    }

    /**
     * Adds a notification to the database.
     * @param string $addressee the username of the user who received a notification.
     * @param NotificationType $ntype the notification type.
     * @param array $origin notification parameters.
     * @throws Exception if couldn't process the request.
     */
    public function addNotification(string $addressee, NotificationType $ntype, array $origin)
    {
        if ($stmt = $this->db->prepare("INSERT INTO NOTIFICA (destinatario, tipo, origine) VALUES (?,?,?)")) {
            $parameters = json_encode($origin);
            $type = $ntype->name;
            $stmt->bind_param('sss', $addressee, $type, $parameters);
            return $stmt->execute();
        }
        throw new Exception("Error Processing Request", 1);
    }

    /**
     * Deletes a notification from the database.
     * @param int $id the notification's id.
     * @return bool true if the notification was deleted successfully, false otherwise.
     * @throws Exception if couldn't process the request.
     */
    public function deleteNotification(int $id)
    {
        if ($stmt = $this->db->prepare("DELETE FROM NOTIFICA WHERE id = ?")) {
            $stmt->bind_param('i', $id);
            return $stmt->execute();
        }
        throw new Exception("Error Processing Request", 1);
    }

    /**
     * Deletes all notifications of a user.
     * @param string $username the user's username.
     * @return bool true if all the notification were deleted successfully, false otherwise.
     * @throws Exception if couldn't process the request.
     */
    public function deleteAllNotifications(string $username)
    {
        if ($stmt = $this->db->prepare("DELETE FROM NOTIFICA WHERE destinatario = ?")) {
            $stmt->bind_param('s', $username);
            return $stmt->execute();
        }
        throw new Exception("Error Processing Request", 1);
    }

    /**
     * Returns the notification specified by id.
     * @param int $id the notification's id.
     * @return array an array of associative arrays containing the notification.
     * @throws Exception if couldn't process the request.
     */
    public function getNotification(int $id): array
    {
        if ($stmt = $this->db->prepare("SELECT * FROM NOTIFICA WHERE id = ?")) {
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        throw new Exception("Error Processing Request", 1);
    }

    /**
     * Returns the comment of the given id
     * @param int $id of the comment
     * @return array of results
     */
    public function getComment($id)
    {
        if ($stmt = $this->db->prepare("SELECT * FROM `COMMENTO` WHERE id_commento=?")) {
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }
    }

    /**
     * Adds a new comment to a post.
     * @param string $username the user who made the comment.
     * @param string $text the comment's text.
     * @param int $id_post the post's id.
     * @return int the comment id or -1 if insertion went wrong
     */
    public function addNewComment(string $username, string $text, int $id_post)
    {
        if ($stmt = $this->db->prepare("INSERT INTO COMMENTO (testo, id_post, username) VALUES (?, ?, ?)")) {
            $stmt->bind_param('sis', $text, $id_post, $username);
            if ($stmt->execute() == true) {
                return $this->db->insert_id;
            }
            return -1;
        } else {
            return -1;
        }
    }


    /**
     * Adds a new answer to a comment.
     * @param string $username the user who made the answer.
     * @param string $text the answer's text.
     * @param int $id_post the post's id.
     * @param int $id_padre the id of the answered comment.
     * @return int the comment id or -1 if insertion went wrong
     */
    public function addNewCommentToComment(string $username, int $id_padre, string $text, int $id_post)
    {
        if ($stmt = $this->db->prepare("INSERT INTO COMMENTO (testo, id_padre, id_post, username) VALUES (?, ?, ?, ?)")) {
            $stmt->bind_param('siis', $text, $id_padre, $id_post, $username);
            if ($stmt->execute() == true) {
                return $this->db->insert_id;
            }
            return -1;
        } else {
            return -1;
        }
    }

    /**
     * Gets n post's comment older than a given timestamp.
     * @param int $id the post's id
     * @param int $n number of comments to load.
     * @param int $offset the comments offset.
     * @param string $timestamp time limit.
     * @return array an array of associative arrays containing the comments.
     */
    public function getCommentOffset(int $id, int $n, int $offset, string $timestamp)
    {
        if ($stmt = $this->db->prepare("SELECT * FROM `COMMENTO` WHERE id_post=? AND id_padre IS NULL AND timestamp <? ORDER BY timestamp DESC LIMIT ? OFFSET ?")) {
            $stmt->bind_param('isii', $id, $timestamp, $n, $offset);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }
    }

    /**
     * Gets n comment's answers older than a given timestamp.
     * @param int $id the post's id.
     * @param int $id_comment the comment's id.
     * @param int $n number of answers to load.
     * @param int $offset the answers offset.
     * @param string $timestamp time limit.
     *@return array an array of associative arrays containing the answers.
     */
    public function getCommentAnswerOffset(int $id, int $id_comment, int $n, int $offset, string $timestamp)
    {
        if ($stmt = $this->db->prepare("SELECT * FROM `COMMENTO` WHERE id_post=? AND id_padre=? AND timestamp <? ORDER BY timestamp DESC LIMIT ? OFFSET ?")) {
            $stmt->bind_param('iisii', $id, $id_comment, $timestamp, $n, $offset);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }
    }

    /**
     * Gets n user's saved posts with an offset.
     * @param string $username the user's username.
     * @param int $n number of saved post to load.
     * @param int $offset the posts offset.
     * @return array an array of associative arrays containing the user's saved posts.
     * @throws Exception if couldn't process the request.
     */
    public function getSavedPosts(string $username, int $n, int $offset)
    {
        if ($stmt = $this->db->prepare("SELECT POST.* FROM POST JOIN SALVATI ON POST.id_post=SALVATI.id_post WHERE SALVATI.username=? ORDER BY POST.timestamp DESC LIMIT ? OFFSET ?")) {
            $stmt->bind_param('sii', $username, $n, $offset);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        throw new Exception("Error Processing Request", 1);
    }

    /**
     * @param string $username the user for whomst load the post.
     * @param int $limit number of posts to get.
     * @param int $offset posts offset.
     * @param string $startTime a string that represents a timestamp.
     * @return array an array of $n or less posts of people/animals that the user follows.
     */
    public function getPostsForUser(string $username, int $limit, int $offset, string $startTime)
    {
        $query = "SELECT DISTINCT p.*
        FROM POST p
        LEFT JOIN RIGUARDA r ON p.id_post = r.id_post
        LEFT JOIN SEGUE_ANIMALE sa ON r.animale = sa.followed
        LEFT JOIN SEGUE_PERSONA sp ON p.username = sp.followed
        WHERE (sp.follower = ? OR sa.follower = ?)
          AND p.username != ?
          AND p.timestamp >= ? - INTERVAL 2 DAY
        ORDER BY p.timestamp DESC
        LIMIT ?, ?";

        if($stmt = $this->db->prepare($query)) {
            $stmt->bind_param('ssssii', $username, $username, $username, $startTime, $offset, $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }
    }

    /**
     * @param string $username the user for whomst load the post.
     * @param int $limit number of posts to get.
     * @param int $offset posts offset.
     * @param string $startTime a string that represents a timestamp.
     * @return array an array of $n or less posts of people/animals that the user does not follow. 
     */
    public function getRecentPostsForUser(string $username, int $limit, int $offset, string $startTime) 
    {
        $query = "SELECT p.*
        FROM POST p
        LEFT JOIN RIGUARDA r ON p.id_post = r.id_post
        LEFT JOIN LIKES l ON p.id_post = l.id_post
        LEFT JOIN SEGUE_PERSONA sp ON p.username = sp.followed AND sp.follower = ?
        LEFT JOIN SEGUE_ANIMALE sa ON r.animale = sa.followed AND sa.follower = ?
        WHERE sp.follower IS NULL AND sa.follower IS NULL
          AND p.username != ?
          AND p.timestamp >= ? - INTERVAL 2 DAY
        GROUP BY p.id_post
        ORDER BY COUNT(l.id_post) DESC, p.timestamp DESC
        LIMIT ?, ?";

        if($stmt = $this->db->prepare($query)) {
            $stmt->bind_param('ssssii', $username, $username, $username, $startTime, $offset, $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }
    }

    /**
     * @param string $username the user for whomst load the post.
     * @param int $limit number of posts to get.
     * @param int $offset posts offset.
     * @param int $seed used to set the sql RAND seed
     * @param string $startTime a string that represents a timestamp.
     * @return array an array of $n or less random posts of people/animals.
     */
    public function getOlderRandomPosts(string $username, int $limit, int $offset, int $seed, string $startTime) 
    {
        if($stmt = $this->db->prepare("SELECT * FROM POST WHERE timestamp < ? - INTERVAL 2 DAY AND username != ? ORDER BY RAND(?) LIMIT ?, ?")) {
            $stmt->bind_param('ssiii', $startTime, $username, $seed, $offset, $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }
    }

    /**
     * Gets all the followers of an animal.
     * @param string $animal the animal's username.
     * @return array an array of associative arrays containing the followers' username.
     * @throws Exception if couldn't process the request.
     */
    public function getAllAnimalFollowers(string $animal)
    {
        if ($stmt = $this->db->prepare("SELECT follower FROM SEGUE_ANIMALE WHERE followed=?")) {
            $stmt->bind_param('s', $animal);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_NUM);
        } else {
            throw new Exception("Error Processing Request", 1);
        }
    }

    /**
     * Inserts a new entry in password reset table.
     * @param string $email the email address of the account that requested a password reset.
     * @param string $code the reset code.
     * @return bool true if the code was successfully added, false otherwise.
     */
    public function newResetCode(string $email, string $code)
    {
        if ($stmt = $this->db->prepare("INSERT INTO PASSWORD_RESET (email, generated_key) VALUES (?, ?)")) {
            $stmt->bind_param('ss', $email, $code);
            return $stmt->execute();
        } else {
            return false;
        }
    }

    /**
     * Returns a password reset entry's parameters.
     * @param string $code the reset code.
     * @return array an array of associative arrays containing the password reset's parameters.
     */
    public function getResetCodeInfo(string $code){
        if($stmt = $this->db->prepare("SELECT * FROM PASSWORD_RESET WHERE generated_key=?")) {
            $stmt->bind_param('s', $code);
            $stmt->execute();
            $result = $stmt->get_result();
            $tmp=$result->fetch_all(MYSQLI_ASSOC);
            if(empty($tmp)==false){
                return $tmp[0];
            }else{
                return array();
            }
        } else {
            return array();
        }
    }

    /**
     * Returns all the reset codes associated to an email.
     * @param string $email the email address.
     * @return array an array of associative arrays containing the password reset entries.
     */
    public function getAllResetCodesForEmail(string $email){
        if($stmt = $this->db->prepare("SELECT * FROM PASSWORD_RESET WHERE email=? ORDER BY generated_on DESC")) {
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }
    }

    /**
     * Deletes all the reset codes associated to an email address.
     * @param string $email the email address.
     * @return bool true if the reset codes were deleted successfully, false otherwise.
     */
    public function removeAllPasswordCodes(string $email)
    {
        if ($stmt = $this->db->prepare("DELETE FROM PASSWORD_RESET WHERE email = ?")) {
            $stmt->bind_param('s', $email);
            return $stmt->execute();
        } else {
            return false;
        }
    }

    /**
     * Returns the profile a user follows (both animals and people)
     * @param string $username the username of the user
     * @param int $offset the offset of the query
     * @param int $number the number of profiles to get
     * @return array of profiles
     */
    public function getFollowedProfiles($username, $offset, $number){
        if($stmt = $this->db->prepare("SELECT 'person' AS type, P.username, P.immagine FROM SEGUE_PERSONA 
                                        JOIN PERSONA AS P ON SEGUE_PERSONA.followed=P.username
                                        WHERE follower=? UNION 
                                        SELECT 'animal' AS type, A.username, A.immagine FROM SEGUE_ANIMALE 
                                        JOIN ANIMALE AS A ON SEGUE_ANIMALE.followed=A.username
                                        WHERE follower=? ORDER BY username LIMIT ?, ?")) {
            $stmt->bind_param('ssii', $username, $username, $offset, $number);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }
    }

    /**
     * Deletes the specified post.
     * @param int $id the post's id.
     * @return bool true if the post was successfully deleted, false otherwise. 
     */
    public function deletePost(int $id){
        if($stmt = $this->db->prepare("DELETE FROM POST WHERE id_post = ?")){
            $stmt->bind_param('i', $id);
            return $stmt->execute();
        } else {
            return false;
        }
    }

    /**
     * Disables the given account
     * @param string $user the account to disable
     * @return bool true if the account was was successfully disabled 
     */
    public function disableAccount(string $user){
        if ($stmt = $this->db->prepare("UPDATE PERSONA SET disabilitato = true WHERE username = ?")) {
            $stmt->bind_param("s", $user);
            return $stmt->execute();
        } else {
            return false;
        }
    }

    /**
     * Enables the given account
     * @param string $user the account to disable
     * @return bool true if the account was was successfully disabled 
     */
    public function enableAccount(string $user){
        if ($stmt = $this->db->prepare("UPDATE PERSONA SET disabilitato = false WHERE username = ?")) {
            $stmt->bind_param("s", $user);
            return $stmt->execute();
        } else {
            return false;
        }
    }

    /**
     * Returns if an account is disabled
     * @param string $user the account 
     * @return bool true if the account is disabled
     */
    public function isAccountDisabled(string $user){
        if($stmt = $this->db->prepare("SELECT disabilitato FROM PERSONA WHERE username=?")) {
            $stmt->bind_param('s', $user);
            $stmt->execute();
            $result = $stmt->get_result();
            $tmp=$result->fetch_all(MYSQLI_ASSOC);
            if(empty($tmp)){
                return false;
            }else{
                return $tmp[0]["disabilitato"];
            }
        } else {
            return false;
        }
    }

    /**
     * Deletes all previous login attempts of a user
     * @param string $username the account 
     * @return bool false if something went wrong
     */
    public function deleteAllLoginAttempts(string $username)
    {
        if($stmt = $this->db->prepare("DELETE FROM TENTATIVO_LOGIN WHERE username = ? ")){
        $stmt->bind_param('s', $username);
        return $stmt->execute();
        } else {
            return false;
        }
    }
}
