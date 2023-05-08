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
}
