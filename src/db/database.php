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

    public function getAnimalsFromUser($username){
        if($stmt = $this->db->prepare("SELECT username, immagine FROM animale INNER JOIN possiede ON animale.username = possiede.animale WHERE possiede.persona = ?")){
            $stmt->bind_param('s', $username);
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
        if($stmt = $this->db->prepare("SELECT persona AS username FROM possiede WHERE animale = ?")) {
            $stmt->bind_param('s',$animal);
            $stmt->execute();
            $result = $stmt->get_result();
            return array_column($result->fetch_all(MYSQLI_ASSOC), "username");
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
}
