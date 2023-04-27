<!DOCTYPE html>
<html lang="it">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    
    <link rel="stylesheet" type="text/css" href="../css/style.css" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">

    <title><?php
    if (isset($templateParams["title"])) {
        echo($templateParams["title"]);
    }
    ?></title>

</head>

<body>
    <nav class="navbar navbar-expand bg-light fixed-top">
        <div class="container-fluid">
            <div class="collapse navbar-collapse">
                <div class="navbar-nav nav-fill">
                    <a class="nav-link <?php isActive("home")?>" href="home/home.php">Home</a>
                    <a class="nav-link <?php isActive("profile")?>" href="profile.php">Profilo</a>
                    <a class="nav-link <?php isActive("search")?>" href="search.php">Search</a>
                </div>
            </div>
        </div>
    </nav>
    <?php
    if (isset($templateParams["page"])) {
        require_once($templateParams["page"]);
    }
    ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
<script src="/js/script.js" type="text/javascript"></script>
<script class="jsbin" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script class="jsbin" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.0/jquery-ui.min.js"></script>
</body>
</html>
