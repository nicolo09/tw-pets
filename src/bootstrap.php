<?php
/* Path to image dir, to be used when uploading images */
define("BASE_FOLDER", __DIR__ . "/");
define("IMG_DIR", "img/");

require_once("utils/functions.php");
sec_session_start();
require_once("db/database.php");
$dbh = new DatabaseHelper("localhost", "root", "", "twpets", 3306);
