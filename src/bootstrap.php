<?php
/* Path to image dir, to be used when uploading images */
define("BASE_FOLDER", __DIR__ . "/");
define("IMG_DIR", "img/");
define("DEFAULT_PET_IMG", "default_pet_image.png");
define("DEFAULT_USER_IMG", "default.jpg");
define("MAX_NOTIFICATIONS", 99);



require_once("utils/functions.php");
sec_session_start();
require_once("db/database.php");
$dbh = new DatabaseHelper("localhost", "root", "", "twpets", 3306);
