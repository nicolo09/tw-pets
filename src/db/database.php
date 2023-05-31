<?php
class DatabaseHelper
{
    private $db;

    public function __construct($servername, $username, $password, $dbname, $port)
    {
        $this->db = new mysqli($servername, $username, $password, $dbname, $port);
        if ($this->db->connect_error) {
            die("Connection failed: " . $this->db->connect_error);
        }
    }

    public function getUser($email)
    {
        // Usando statement sql 'prepared' non sarà possibile attuare un attacco di tipo SQL injection.
        if ($stmt = $this->db->prepare("SELECT username, password FROM persona WHERE email = ? LIMIT 1")) {
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        }
    }

    public function getMutualFollowers($username)
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

    public function getPersonsLike($username, $offset)
    {
        $value = "%" . $username . "%";

        $query = "SELECT p.username, p.immagine
        FROM PERSONA p
        LEFT JOIN SEGUE_PERSONA sp ON p.username = sp.followed
        WHERE p.username LIKE ?
        GROUP BY p.username
        ORDER BY COUNT(sp.follower) DESC, p.username 
        LIMIT $offset, 10";

        if ($stmt = $this->db->prepare($query)) {
            $stmt->bind_param('s', $value);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }
    }

    public function getAnimalsLike($username, $offset)
    {
        $value = "%" . $username . "%";

        $query = "SELECT a.username, a.immagine
        FROM ANIMALE a
        LEFT JOIN SEGUE_ANIMALE sa ON a.username = sa.followed
        WHERE a.username LIKE ?
        GROUP BY a.username
        ORDER BY COUNT(sa.follower) DESC, a.username
        LIMIT $offset, 10";

        if ($stmt = $this->db->prepare($query)) {
            $stmt->bind_param('s', $value);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }
    }

    public function getPersonFollowers($username, $offset)
    {

        $query = "SELECT P.username, P.immagine
        FROM PERSONA P
        LEFT JOIN SEGUE_PERSONA SP ON P.username = SP.followed
        JOIN (
            SELECT P.username
            FROM PERSONA P
            JOIN SEGUE_PERSONA SP ON p.username = SP.follower
            WHERE sp.followed = ?
        ) AS subquery ON P.username = subquery.username
        GROUP BY P.username
        ORDER BY COUNT(SP.follower) DESC, P.username
        LIMIT $offset, 30";

        if ($stmt = $this->db->prepare($query)) {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }
    }

    public function getAnimalFollowers($username, $offset)
    {

        $query = "SELECT P.username, P.immagine
        FROM PERSONA P
        LEFT JOIN SEGUE_PERSONA SP ON P.username = SP.followed
        JOIN (
            SELECT P.username
            FROM PERSONA P
            JOIN SEGUE_ANIMALE SA ON p.username = SA.follower
            LEFT JOIN POSSIEDE PO ON PO.persona = SA.follower AND PO.animale = ?
            WHERE SA.followed = ? AND PO.animale IS NULL
        ) AS subquery ON P.username = subquery.username
        GROUP BY P.username
        ORDER BY COUNT(SP.follower) DESC, P.username
        LIMIT $offset, 30";

        if ($stmt = $this->db->prepare($query)) {
            $stmt->bind_param('ss', $username, $username);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }
    }

    public function addAnimal($username, $type, $img, $description)
    {
        if ($stmt = $this->db->prepare("INSERT INTO animale (username, tipo, immagine, descrizione) VALUES (?, ?, ?, ?)")) {
            $stmt->bind_param('ssss', $username, $type, $img, $description);
            return $stmt->execute();
        } else {
            return false;
        }
    }

    public function updateAnimal($username, $type, $img, $description)
    {
        if ($stmt = $this->db->prepare("UPDATE animale SET tipo = ?, immagine = ?, descrizione = ? WHERE username = ?")) {
            $stmt->bind_param("ssss", $type, $img, $description, $username);
            return $stmt->execute();
        } else {
            return false;
        }
    }

    public function updateUserProfile($username, $employment, $img, $description)
    {
        if ($stmt = $this->db->prepare("UPDATE persona SET impiego = ?, immagine = ?, descrizione = ? WHERE username = ?")) {
            $stmt->bind_param("ssss", $employment, $img, $description, $username);
            return $stmt->execute();
        } else {
            return false;
        }
    }

    public function getAnimals($animal)
    {
        if ($stmt = $this->db->prepare("SELECT * FROM animale WHERE username = ?")) {
            $stmt->bind_param('s', $animal);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        }
    }

    public function registerOwnership($owner, $animal)
    {
        if (count($this->getUserFromName($owner)) == 1) {
            if ($stmt = $this->db->prepare("INSERT INTO possiede (persona, animale) VALUES (?, ?)")) {
                $stmt->bind_param('ss', $owner, $animal);
                return $stmt->execute();
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function deleteOwnership($owner, $animal)
    {
        if ($stmt = $this->db->prepare("DELETE FROM possiede WHERE persona = ? AND animale = ?")) {
            $stmt->bind_param('ss', $owner, $animal);
            return $stmt->execute();
        } else {
            return false;
        }
    }

    public function getOwners($animal)
    {

        $query = "SELECT P.username, P.immagine
        FROM PERSONA P
        LEFT JOIN SEGUE_PERSONA SP ON P.username = SP.followed
        JOIN (
            SELECT p.username
            FROM persona p
            JOIN possiede po ON p.username = po.persona
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

    /* Returns true if the user owns the given animal */
    public function checkOwnership($owner, $animal)
    {
        if ($stmt = $this->db->prepare("SELECT * FROM possiede WHERE persona = ? AND animale = ?")) {
            $stmt->bind_param('ss', $owner, $animal);
            $stmt->execute();
            $result = $stmt->get_result();
            return count($result->fetch_all(MYSQLI_ASSOC)) == 1;
        } else {
            return false;
        }
    }

    public function getPassword($username)
    {
        // Usando statement sql 'prepared' non sarà possibile attuare un attacco di tipo SQL injection.
        if ($stmt = $this->db->prepare("SELECT password FROM persona WHERE username = ? LIMIT 1")) {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        }
    }

    public function addLoginAttempt($username)
    {
        if ($stmt = $this->db->prepare("INSERT INTO tentativo_login (timestamp, username) VALUES (NOW(), ?)")) {
            $stmt->bind_param('s', $username);
            $stmt->execute();
        }
    }

    public function getLoginAttempts($username, $from)
    {
        $stmt = $this->db->prepare("DELETE FROM tentativo_login WHERE username = ? AND timestamp < FROM_UNIXTIME($from)");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        if ($stmt = $this->db->prepare("SELECT timestamp FROM tentativo_login WHERE username = ? AND timestamp > FROM_UNIXTIME($from)")) {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        }
    }

    public function getUserFromName($username)
    {
        if ($stmt = $this->db->prepare("SELECT * FROM persona WHERE username = ? LIMIT 1")) {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        }
    }

    public function addUser($user, $password, $email)
    {
        if ($stmt = $this->db->prepare("INSERT INTO persona (username, password, email) VALUES (?, ?, ?)")) {
            $password = password_hash($password, PASSWORD_DEFAULT);
            $stmt->bind_param('sss', $user, $password, $email);
            if ($stmt->execute()) {
                $stmt = $this->db->prepare("INSERT INTO impostazione(username) VALUES (?)");
                $stmt->bind_param('s', $user);
                return $stmt->execute();
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function updateSetting($username, $setting, $value)
    {
        if ($stmt = $this->db->prepare("UPDATE impostazione SET `$setting` = ? WHERE username = ?")) {
            $value = $value == "true" ? 1 : 0;
            $stmt->bind_param('is', $value, $username);

            return $stmt->execute();
        } else {
            return false;
        }
    }

    public function getSettings($username)
    {
        if ($stmt = $this->db->prepare("SELECT * FROM impostazione WHERE username = ? LIMIT 1")) {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        }
    }

    public function changeEmail($oldEmail, $newEmail)
    {
        if ($stmt = $this->db->prepare("UPDATE persona SET email = ? WHERE email = ?")) {
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
        if ($stmt = $this->db->prepare("INSERT INTO post (immagine, alt, username, testo) VALUES (?, ?, ?, ?)")) {
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
        if ($stmt = $this->db->prepare("SELECT * FROM possiede JOIN animale ON animale.username=possiede.animale WHERE persona = ?")) {
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
        if ($stmt = $this->db->prepare("INSERT INTO riguarda (id_post, animale) VALUES (?,?)")) {
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
        if ($stmt = $this->db->prepare("SELECT username, descrizione, immagine, impiego FROM persona WHERE username = ?")) {
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
        if ($stmt = $this->db->prepare("SELECT * FROM post WHERE username = ? ORDER BY post.timestamp DESC")) {
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
        if ($stmt = $this->db->prepare("SELECT COUNT(username) FROM persona WHERE username=?")) {
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
        if ($stmt = $this->db->prepare("SELECT followers FROM segue_persona WHERE followed=?")) {
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
        if ($stmt = $this->db->prepare("SELECT * FROM segue_persona WHERE followed=? AND follower=?")) {
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
        if ($stmt = $this->db->prepare("SELECT COUNT(username) FROM animale WHERE username=?")) {
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
        if ($stmt = $this->db->prepare("SELECT username, descrizione, immagine, tipo FROM animale WHERE username = ?")) {
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
        if ($stmt = $this->db->prepare("SELECT * FROM riguarda JOIN post ON riguarda.id_post=post.id_post WHERE riguarda.animale=?")) {
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
        if ($stmt = $this->db->prepare("SELECT * FROM segue_animale WHERE follower=? AND followed=?")) {
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
        if ($stmt = $this->db->prepare("INSERT INTO segue_persona (followed, follower) VALUES (?,?)")) {
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
        if ($stmt = $this->db->prepare("DELETE FROM segue_persona WHERE followed=? AND follower=?")) {
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
        if ($stmt = $this->db->prepare("INSERT INTO segue_animale (followed, follower) VALUES (?,?)")) {
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
        if ($stmt = $this->db->prepare("DELETE FROM segue_animale WHERE followed=? AND follower=?")) {
            $stmt->bind_param('ss', $animal, $follower);
            return $stmt->execute();
        } else {
            return false;
        }
    }
    public function changePassword($username, $newPassword)
    {
        if ($stmt = $this->db->prepare("UPDATE persona SET password = ? WHERE username = ?")) {
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
        if ($stmt = $this->db->prepare("SELECT post.id_post, post.immagine, post.alt, post.testo, post.timestamp, persona.username, persona.immagine as immagineprofilo FROM post JOIN persona ON post.username=persona.username WHERE post.id_post=?")) {
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
        if ($stmt = $this->db->prepare("SELECT COUNT(*) FROM likes WHERE id_post=?")) {
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
        if ($stmt = $this->db->prepare("SELECT COUNT(*) FROM `likes` WHERE username=? AND id_post=?")) {
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
        if ($stmt = $this->db->prepare("SELECT COUNT(*) FROM salvati WHERE username=? AND id_post=?")) {
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
        if ($stmt = $this->db->prepare("SELECT COUNT(id_post) FROM post WHERE id_post=?")) {
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
        if ($stmt = $this->db->prepare("INSERT INTO likes (id_post, username) VALUES (?,?)")) {
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
        if ($stmt = $this->db->prepare("DELETE FROM likes WHERE id_post=? AND username=?")) {
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
        if ($stmt = $this->db->prepare("INSERT INTO salvati (id_post, username) VALUES (?,?)")) {
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
        if ($stmt = $this->db->prepare("DELETE FROM salvati WHERE id_post=? AND username=?")) {
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
        if ($stmt = $this->db->prepare("SELECT animale FROM riguarda WHERE id_post=?")) {
            $stmt->bind_param('i', $post);

            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }
    }

    /**
     * Recupera i post preferiti dell'utente
     * @param string $username username dell'utente di cui si vuole ottenere i post preferiti
     * @param int $from timestamp di inizio del periodo di cui si vuole ottenere i post preferiti
     * @param int $offset offset dei post preferiti
     * @param int $n numero di post preferiti da ottenere
     * @return array array associativo contenente i post preferiti dell'utente
     */
    public function getFavoritePosts(string $username, int $from, int $offset, int $n)
    {
        if ($stmt = $this->db->prepare("SELECT * FROM salvati JOIN post ON salvati.id_post=post.id_post WHERE salvati.username=? AND post.timestamp>? ORDER BY timestamp DESC LIMIT ?,?")) {
            $stmt->bind_param('siii', $username, $from, $offset, $n);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        throw new Exception("Errore nella query");
    }



    /**
     * Ritorna i n commenti più recenti lasciati sul post
     * @param int $id_post post di cui si vogliono caricare i commenti
     * @param int $n numero commenti da caricare
     * @return array vettore di commenti
     */
    public function getMostRecentComments(int $id_post, int $n)
    {
        if ($stmt = $this->db->prepare("SELECT * FROM `commento` WHERE id_padre IS NULL AND id_post=? ORDER BY timestamp DESC LIMIT ?")) {
            $stmt->bind_param('ii', $id_post, $n);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }
    }

    /**
     * Ritorna tutti commenti in ordine dai più recenti lasciati sul post
     * @param int $id_post post di cui si vogliono caricare i commenti
     * @return array vettore di commenti
     */
    public function getAllMostRecentComments(int $id_post)
    {
        if ($stmt = $this->db->prepare("SELECT * FROM `commento` WHERE id_padre IS NULL AND id_post=? ORDER BY timestamp DESC")) {
            $stmt->bind_param('i', $id_post);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }
    }

    /**
     * Ritorna se il commento ha "commenti figli"
     * @param int $id_comment l'identificatore del commento padre
     * @return array vuoto se il commento non ha "commenti figli"
     */
    public function doesCommentHaveAnswers(int $id_comment)
    {
        if ($stmt = $this->db->prepare("SELECT COUNT(*) FROM `commento` WHERE id_padre=? ORDER BY timestamp DESC")) {
            $stmt->bind_param('i', $id_comment);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }
    }
    /**
     * Returns true if the user has more than X notifications
     * @param string $username
     * @param int $x
     * @return bool
     */
    public function hasMoreThanXNotifications(string $username, int $x)
    {
        if ($stmt = $this->db->prepare("SELECT COUNT(*)>? FROM notifica WHERE destinatario = ?")) {
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
     * Returns the number of notifications of the user
     * @param string $username
     * @return int
     */
    public function getNumberOfNotifications(string $username): int
    {
        if ($stmt = $this->db->prepare("SELECT COUNT(*) FROM notifica WHERE destinatario = ?")) {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $tmp = $result->fetch_all(MYSQLI_NUM);
            return $tmp[0][0];
        }
        throw new Exception("Error Processing Request", 1);
    }

    /**
     * Returns the first n notifications of the user ordered by timestamp with offset o
     * @param string $username
     * @param int $n how many notifications to return
     * @param int $o offset
     * @return array
     */
    public function getNotifications(string $username, int $n, int $o): array
    {
        if ($stmt = $this->db->prepare("SELECT * FROM notifica WHERE destinatario = ? ORDER BY timestamp DESC LIMIT $o, $n")) {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        throw new Exception("Error Processing Request", 1);
    }

    /**
     * Adds a notification to the database
     * @param string $destinatario
     * @param NotificationType $tipo
     * @param string $origine notification parameters
     */
    public function addNotification(string $destinatario, NotificationType $tipo, array $origine)
    {
        if ($stmt = $this->db->prepare("INSERT INTO notifica (destinatario, tipo, origine) VALUES (?,?,?)")) {
            $parameters = json_encode($origine);
            $type = $tipo->name;
            $stmt->bind_param('sss', $destinatario, $type, $parameters);
            return $stmt->execute();
        }
        throw new Exception("Error Processing Request", 1);
    }

    /**
     * Deletes a notification from the database
     * @param int $id
     * @return bool
     */
    public function deleteNotification(int $id)
    {
        if ($stmt = $this->db->prepare("DELETE FROM notifica WHERE id = ?")) {
            $stmt->bind_param('i', $id);
            return $stmt->execute();
        }
        throw new Exception("Error Processing Request", 1);
    }

    /**
     * Deletes all notifications of a user
     * @param string $username
     * @return bool
     */
    public function deleteAllNotifications(string $username)
    {
        if ($stmt = $this->db->prepare("DELETE FROM notifica WHERE destinatario = ?")) {
            $stmt->bind_param('s', $username);
            return $stmt->execute();
        }
        throw new Exception("Error Processing Request", 1);
    }

    /**
     * Returns the notification specified by id
     * @param int $id
     * @return array
     */
    public function getNotification(int $id): array
    {
        if ($stmt = $this->db->prepare("SELECT * FROM notifica WHERE id = ?")) {
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
        if ($stmt = $this->db->prepare("SELECT * FROM `commento` WHERE id_commento=?")) {
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }
    }

    /**
     * Inserisce un commento
     * @param string $username l'utente che crea il commento
     * @param string $text il testo del commento
     * @param int $id_post il post a cui fa il commento
     * @return bool true se l'inserimento è andato a buon fine
     */
    public function addNewComment(string $username, string $text, int $id_post)
    {
        if ($stmt = $this->db->prepare("INSERT INTO commento (testo, id_post, username) VALUES (?, ?, ?)")) {
            $stmt->bind_param('sis', $text, $id_post, $username);
            return $stmt->execute();
        } else {
            return false;
        }
    }


    /**
     * Inserisce un commento
     * @param string $username l'utente che crea il commento
     * @param string $text il testo del commento
     * @param int $id_post il post a cui fa il commento
     * @param int $id_padre il commento a cui risponde
     * @return bool true se l'inserimento è andato a buon fine
     */
    public function addNewCommentToComment(string $username, int $id_padre, string $text, int $id_post)
    {
        if ($stmt = $this->db->prepare("INSERT INTO commento (testo, id_padre, id_post, username) VALUES (?, ?, ?, ?)")) {
            $stmt->bind_param('siis', $text, $id_padre, $id_post, $username);
            return $stmt->execute();
        } else {
            return false;
        }
    }

    /**
     * Ritorna n commenti del post con offset più vecchi del timestamp fornito
     * @param int $id del post
     * @param int $n numero commenti da caricare
     * @param int $offset l'offset dei commenti
     * @param string $timestamp del commento più recente
     * @return array dei commenti
     */
    public function getCommentOffset(int $id, int $n, int $offset, string $timestamp)
    {
        if ($stmt = $this->db->prepare("SELECT * FROM `commento` WHERE id_post=? AND id_padre IS NULL AND timestamp <? ORDER BY timestamp DESC LIMIT ? OFFSET ?")) {
            $stmt->bind_param('isii', $id, $timestamp, $n, $offset);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }
    }

    /**
     * Returns the comment of the given id
     * @param int $id of the comment
     * @return array of results
     */
    public function getAllMostRecentCommentsAfter(int $id, string $timestamp)
    {
        if ($stmt = $this->db->prepare("SELECT * FROM `commento` WHERE id_padre IS NULL AND id_post=? AND timestamp <? ORDER BY timestamp DESC")) {
            $stmt->bind_param('is', $id, $timestamp);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }
    }

    /**
     * Ritorna n commenti in risposta al post con offset più vecchi del timestamp fornito
     * @param int $id del post
     * @param int $id_comment del commento
     * @param int $n numero commenti da caricare
     * @param int $offset l'offset dei commenti
     * @param string $timestamp del commento più recente
     *@return array dei commenti
     */
    public function getCommentAnswerOffset(int $id, int $id_comment, int $n, int $offset, string $timestamp)
    {
        if ($stmt = $this->db->prepare("SELECT * FROM `commento` WHERE id_post=? AND id_padre=? AND timestamp <? ORDER BY timestamp DESC LIMIT ? OFFSET ?")) {
            $stmt->bind_param('iisii', $id, $id_comment, $timestamp, $n, $offset);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }
    }

    /**
     * Ritorna n post salvati dall'utente con offset 
     * @param string $username dell'utente
     * @param int $n numero post da caricare
     * @param int $offset l'offset dei post
     * @return array dei post
     */
    public function getSavedPosts(string $username, int $n, int $offset)
    {
        if ($stmt = $this->db->prepare("SELECT * FROM post JOIN salvati ON post.id_post=salvati.id_post WHERE salvati.username=? ORDER BY post.timestamp DESC LIMIT ? OFFSET ?")) {
            $stmt->bind_param('sii', $username, $n, $offset);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        throw new Exception("Error Processing Request", 1);
    }

    /**
     * @param string $username the user for whomst load the post.
     * @param int $n number of posts to get.
     * @param int $offset posts offset.
     * @param string $startTime a string that represents a timestamp.
     * @return array an array of $n or less posts of people/animals that the user follows.
     */
    public function getPostsForUser(string $username, int $n, int $offset, $startTime)
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
            $stmt->bind_param('ssssii', $username, $username, $username, $startTime, $offset, $n);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }
    }

    /**
     * @param string $username the user for whomst load the post.
     * @param int $n number of posts to get.
     * @param int $offset posts offset.
     * @param string $startTime a string that represents a timestamp.
     * @return array an array of $n or less posts of people/animals that the user does not follow. 
     */
    public function getRecentPostsForUser(string $username, int $n, int $offset, $startTime) 
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
            $stmt->bind_param('ssssii', $username, $username, $username, $startTime, $offset, $n);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }
    }

    /**
     * @param string $username the user for whomst load the post.
     * @param int $n number of posts to get.
     * @param int $offset posts offset.
     * @param int $seed used to set the sql RAND seed
     * @param string $startTime a string that represents a timestamp.
     * @return array an array of $n or less random posts of people/animals.
     */
    public function getOlderRandomPosts(string $username, int $n, int $offset, int $seed, $startTime) 
    {
        if($stmt = $this->db->prepare("SELECT * FROM POST WHERE timestamp < ? - INTERVAL 2 DAY AND username != ? ORDER BY RAND(?) LIMIT ?, ?")) {
            $stmt->bind_param('ssiii', $startTime, $username, $seed, $offset, $n);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }
    }

}
