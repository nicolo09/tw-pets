<!DOCTYPE html>
<html lang="it">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">

    <!-- CSS -->
    <link rel="stylesheet" href="../css/style.css"> <!-- TODO define better path -->

    <!-- TODO add title templateParams["title"] -->
    <?php
    $max = countNFiles("favicon/", "ico");
    $n = rand(0, $max - 1); //I file hanno indice 0.... max-1, per un totale di max files
    echo '<link rel="icon" type="image/x-icon" href="favicon/favicon-' . $n . '.ico">';
    ?>
    <title>Pets - Login</title>
</head>

<body>
    <header>
        <h1 class="text-center">🐶PETS🐱</h1>
    </header>
    <?php
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
</body>

</html>