<!DOCTYPE html>
<html lang="it">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" type="text/css" href="../css/style.css" />
    <?php
    $max = countNFiles("favicon/", "png");
    $n = rand(0, $max - 1); //I file hanno indice 0.... max-1, per un totale di max files
    echo '<link rel="icon" type="image/png" sizes="16x16" href="favicon/favicon-16x16-' . $n . '.png">';
    ?>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">

    <!-- jQuery script -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

    <!-- Select2 CSS and scripts -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <title><?php
            if (isset($templateParams["title"])) {
                echo ($templateParams["title"]);
            }
            ?></title>

    <?php
    /*
    $files = glob($dir . '/*.*');
    $file = array_rand($files);
    $icon = $files[$file];
    */
    ?>
    
    <link rel="icon" href="<?php echo $icon ?>" type="image/x-icon" />
</head>

<body>
    <nav class="navbar navbar-expand bg-light fixed-top p-0">
        <div class="container-fluid">
            <div class="collapse navbar-collapse">
                <div class="navbar-nav nav-fill row-cols-3">
                    <a class="nav-link <?php isActive("home") ?>" href="home.php">Home</a>
                    <a class="nav-link <?php isActive("profile") ?>" href="tab-profile.php" id="profile-nav-element">Profilo</a>
                    <a class="nav-link <?php isActive("search") ?>" href="search.php">Search</a>
                </div>
            </div>
        </div>
    </nav>
    <?php
    if (!isset($_COOKIE["cookie-law-accepted"]) || !$_COOKIE["cookie-law-accepted"]==true) {
        echo "<div class=\"justify-content-center d-flex\"><div class=\"alert alert-primary alert-dismissible fade show m-1\" role=\"alert\"> " . COOKIE_MESSAGE . " <a href=cookie.php>Scopri di più</a> <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\" aria-label=\"Close\" id=\"cookie-dismiss-btn\"></button></div></div>";
    }
    if (isset($templateParams["messages"])) {
        foreach ($templateParams["messages"] as $message) {
            echo "<div class=\"desktop-view\"><div class=\"justify-content-center d-flex\"><div class=\"alert alert-success alert-dismissible fade show col-6\" role=\"alert\"> <label class=\"top-page-popup\">" . $message . "</label> <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\" aria-label=\"Close\"></button></div></div></div>";
            echo "<div class=\"mobile-view\"><div class=\" justify-content-center d-flex\"><div class=\"alert alert-success alert-dismissible fade show col-12\" role=\"alert\"> <label class=\"top-page-popup\">" . $message . "</label> <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\" aria-label=\"Close\"></button></div></div></div>";
        }
    }
    if (isset($templateParams["errors"])) {
        foreach ($templateParams["errors"] as $error) {
            echo "<div class=\"desktop-view\"><div class=\"justify-content-center d-flex\"><div class=\"alert alert-danger alert-dismissible fade show col-6\" role=\"alert\"> <label class=\"top-page-popup\">" . $error . "</label> <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\" aria-label=\"Close\"></button></div></div></div>";
            echo "<div class=\"mobile-view\"><div class=\" justify-content-center d-flex\"><div class=\"alert alert-danger alert-dismissible fade show col-12\" role=\"alert\"> <label class=\"top-page-popup\">" . $error . "</label> <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\" aria-label=\"Close\"></button></div></div></div>";
        }
    }
    if (isset($templateParams["page"])) {
        require_once($templateParams["page"]);
    }
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
    <script src="/js/script.js" type="text/javascript"></script>
</body>

</html>