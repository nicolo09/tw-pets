<!DOCTYPE html>
<html lang="it">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="stylesheet" type="text/css" href="css/style.css" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">

    <title>Nuovo post</title>
</head>

<body>
    <nav class="navbar navbar-expand bg-light fixed-top">
        <div class="container-fluid">
            <div class="collapse navbar-collapse">
                <div class="navbar-nav nav-fill">
                    <a class="nav-link" href="home/home.php">Home</a>
                    <a class="nav-link active" href="profile.php">Profilo</a>
                    <a class="nav-link" href="search.php">Search</a>
                </div>
            </div>
        </div>
    </nav>
    <h1>Crea nuovo post</h1>
    <form action="#" method="POST" class="">
        <!--Immagine mostrata qui-->
        <div>
            <img id="show-post-img" src="" alt="Immagine inserita da utente">
        </div>
        <!--Immagine da inserire-->
        <div class="row mt-2">
            <label for="imgpost">Scegli l'immagine del post</label>
            <input type="file" name="imgpost" id="imgpost" accept="jpg, jpeg, png, gif">
        </div>
        <!--Alt immagine-->
        <div class="row mt-2 my-son-textarea">
            <label for="imgalt" class="p-0">Scrivi una breve descrizione dell'immagine scelta:</label>
            <textarea id="imgalt" name="imgalt" maxlenght=50 
            placeholder="Descrizione immagine in meno di 50 caratteri" class="p-0 m-0"></textarea>
        </div>
        <!--Testo da inserire-->
        <div class="row mt-2 my-son-textarea">
            <label for="txtpost" class="p-0">Testo Post:</label>
            <textarea id="txtpost" name="txtpost" maxlenght=100 
            placeholder="Scrivi una descrizione per il tuo post di meno di 100 caratteri" class="p-0 m-0"></textarea>
        </div>
        <!--Animali presenti-->
        <div>
            <button class="btn btn-outline-animals"><img src="img/pets.svg">Animali</button></div>
        </div>
        <!--Invia-->
        <div class="col text-center">
            <button type="submit" formmethod="POST" class="w-100 d-flex justify-content-center mt-5">Pubblica il nuovo post</button>
        </div>
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
</body>

</html>