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

    public function getMutualFollowers($username) {
        $query = "SELECT p.username, p.immagine
        FROM PERSONA p
        INNER JOIN SEGUE_PERSONA sp1 ON p.username = sp1.followed
        INNER JOIN SEGUE_PERSONA sp2 ON p.username = sp2.follower
        WHERE sp1.follower = ? AND sp2.followed = ? ORDER BY p.username ASC";

        if($stmt = $this->db->prepare($query)){
            $stmt->bind_param('ss', $username, $username);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }

    }

    public function getPersonsLike($username, $offset){
        $value = "%".$username."%";

        $query = "SELECT p.username, p.immagine
        FROM PERSONA p
        LEFT JOIN SEGUE_PERSONA sp ON p.username = sp.followed
        WHERE p.username LIKE ?
        GROUP BY p.username
        ORDER BY COUNT(sp.follower) DESC, p.username 
        LIMIT $offset, 10";

        if($stmt = $this->db->prepare($query)) {
            $stmt->bind_param('s', $value);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }
    }

    public function getAnimalsLike($username, $offset){
        $value = "%".$username."%";

        $query = "SELECT a.username, a.immagine
        FROM ANIMALE a
        LEFT JOIN SEGUE_ANIMALE sa ON a.username = sa.followed
        WHERE a.username LIKE ?
        GROUP BY a.username
        ORDER BY COUNT(sa.follower) DESC, a.username
        LIMIT $offset, 10";

        if($stmt = $this->db->prepare($query)) {
            $stmt->bind_param('s', $value);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }
    }

    public function getPersonFollowers($username, $offset){

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

        if($stmt = $this->db->prepare($query)) {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }
    }

    public function getAnimalFollowers($username, $offset){

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

        if($stmt = $this->db->prepare($query)) {
            $stmt->bind_param('ss', $username, $username);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }
    }

    public function addAnimal($username, $type, $img, $description) {
        if ($stmt = $this->db->prepare("INSERT INTO animale (username, tipo, immagine, descrizione) VALUES (?, ?, ?, ?)")) {
            $stmt->bind_param('ssss', $username, $type, $img, $description);
            return $stmt->execute();
        } else {
            return false;
        }
    }

    public function updateAnimal($username, $type, $img, $description){
        if($stmt = $this->db->prepare("UPDATE animale SET tipo = ?, immagine = ?, descrizione = ? WHERE username = ?")){
            $stmt->bind_param("ssss", $type, $img, $description, $username);
            return $stmt->execute();
        } else {
            return false;
        }
    }

    public function getAnimals($animal){
        if($stmt = $this->db->prepare("SELECT * FROM animale WHERE username = ?")){
            $stmt->bind_param('s', $animal);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        }
    }

    public function registerOwnership($owner, $animal) {
        if(count($this->getUserFromName($owner)) == 1) {
            if($stmt = $this->db->prepare("INSERT INTO possiede (persona, animale) VALUES (?, ?)")) {
                $stmt->bind_param('ss', $owner, $animal);
                return $stmt->execute();
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function deleteOwnership($owner, $animal) {
        if($stmt = $this->db->prepare("DELETE FROM possiede WHERE persona = ? AND animale = ?")) {
            $stmt->bind_param('ss', $owner, $animal);
            return $stmt->execute();
        } else {
            return false;
        }
    }

    public function getOwners($animal) {

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

        if($stmt = $this->db->prepare($query)) {
            $stmt->bind_param('s',$animal);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }
    }

    /* Returns true if the user owns the given animal */
    public function checkOwnership($owner, $animal) {
        if($stmt = $this->db->prepare("SELECT * FROM possiede WHERE persona = ? AND animale = ?")) {
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

    /*This function returns the id of the created post or -1 if something went wrong*/
    public function addPost($img, $alt, $txt, $user)
    {
        if ($stmt = $this->db->prepare("INSERT INTO post (immagine, alt, username, testo) VALUES (?, ?, ?, ?)")) {
            $stmt->bind_param('ssss', $img, $alt, $user, $txt);
            if ($stmt->execute() == true) {
                return $this->db->insert_id;
            }
        }
        return -1;
    }

    public function getOwnedAnimals($user)
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

    public function addAnimalToPost($idPost, $username)
    {
        if ($stmt = $this->db->prepare("INSERT INTO riguarda (id_post, animale) VALUES (?,?)")) {
            $stmt->bind_param('is', $idPost, $username);
            return $stmt->execute();
        } else {
            return false;
        }
    }

    public function getUserInfo($username)
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

    public function getUserPosts($username)
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

    public function doesUserExist($username)
    {
        if ($stmt = $this->db->prepare("SELECT COUNT(username) FROM persona WHERE username=?")) {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return -1;
        }
    }

    public function getAllFollowers($username){
        if ($stmt = $this->db->prepare("SELECT followers FROM segue_persona WHERE followed=?")) {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }
    }

    //Ritorna 0 se gli follower non segue followed
    public function doesUserFollowMyAccount($followedUsername, $followerUsername){
        if ($stmt = $this->db->prepare("SELECT * FROM segue_persona WHERE followed=? AND follower=?")) {
            $stmt->bind_param('ss', $followedUsername, $followerUsername);
            $stmt->execute();
            $result = $stmt->get_result();
            $tmp=$result->fetch_all(MYSQLI_ASSOC);
            if(count($tmp)==1){
                return 1;
            }
            return 0;
        } else {
            return 0;
        }
    }

    public function doesAnimalExist($username)
    {
        if ($stmt = $this->db->prepare("SELECT COUNT(username) FROM animale WHERE username=?")) {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return -1;
        }
    }

    public function getAnimalInfo($username)
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

    public function getAnimalPosts($username)
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

    public function doesUserFollowAnimal($username, $animal){
        if ($stmt = $this->db->prepare("SELECT * FROM segue_animale WHERE follower=? AND followed=?")) {
            $stmt->bind_param('ss', $username, $animal);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }
    }

    public function addFollowPerson($followed, $follower){
        if ($stmt = $this->db->prepare("INSERT INTO segue_persona (followed, follower) VALUES (?,?)")) {
            $stmt->bind_param('ss', $followed, $follower);
            return $stmt->execute();
        } else {
            return false;
        }
    }

    public function removeFollowPerson($followed, $follower){
        if ($stmt = $this->db->prepare("DELETE FROM segue_persona WHERE followed=? AND follower=?")) {
            $stmt->bind_param('ss', $followed, $follower);
            return $stmt->execute();
        } else {
            return false;
        }
    }

    public function isAnimalManagedByUser($username, $animale){
        if ($stmt = $this->db->prepare("SELECT COUNT(*) FROM possiede WHERE persona=? AND animale=?")) {
            $stmt->bind_param('ss', $username, $animale);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return array();
        }
    }

    public function addFollowAnimal($animal, $follower){
        if ($stmt = $this->db->prepare("INSERT INTO segue_animale (followed, follower) VALUES (?,?)")) {
            $stmt->bind_param('ss', $animal, $follower);
            return $stmt->execute();
        } else {
            return false;
        }
    }

    public function removeFollowAnimal($animal, $follower){
        if ($stmt = $this->db->prepare("DELETE FROM segue_animale WHERE followed=? AND follower=?")) {
            $stmt->bind_param('ss', $animal, $follower);
            return $stmt->execute();
        } else {
            return false;
        }
    }
    public function changePassword($username, $newPassword){
        if ($stmt = $this->db->prepare("UPDATE persona SET password = ? WHERE username = ?")) {
            $newPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt->bind_param('ss', $newPassword, $username);
            return $stmt->execute();
        } else {
            return false;
        }
    }
}
