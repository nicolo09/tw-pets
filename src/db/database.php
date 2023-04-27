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

    public function addAnimal($username, $type, $img, $description) {
        if ($stmt = $this->db->prepare("INSERT INTO animale (username, tipo, immagine, descrizione) VALUES (?, ?, ?, ?)")) {
            $stmt->bind_param('ssss', $username, $type, $img, $description);
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

    public function registerOwnership($owner, $animal){
        if($stmt = $this->db->prepare("INSERT INTO possiede (persona, animale) VALUES (?, ?)")){
            $stmt->bind_param('ss', $owner, $animal);
            $stmt->execute();
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

    public function addLoginAttempt($username, $time)
    {
        if($stmt = $this->db->prepare("INSERT INTO tentativo_login (timestamp, username) VALUES (?, ?)"))
        {
            $stmt->bind_param('is', $time, $username);
            $stmt->execute();
        }
    }
    
    public function getLoginAttempts($username, $attempts)
    {
        if($stmt = $this->db->prepare("SELECT timestamp FROM tentativo_login WHERE username = ? AND timestamp > '$attempts'"))
        {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC); 
        }
    }
}
