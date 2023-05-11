<div class="card mx-auto col-12 col-lg-10 col-xl-8 g-0 text-center d-flex justify-content-center">
<h1>Crea nuovo post</h1>
<form action="#" method="POST" class="" enctype="multipart/form-data">
    <!--Immagine mostrata qui-->
    <div>
        <img id="imgPreview" src="#" alt="Immagine inserita da utente" class="w-100">
    </div>
    <!--Immagine da inserire-->
    <div class="row mt-2 g-0 d-flex justify-content-center">
        <label for="imgpostinput" class="form-label">Scegli l'immagine del post</label>
        <input required type="file" name="imgpost" id="imgpostinput" accept="image/jpg, image/jpeg, image/png, image/gif" class="form-control w-50 g-0 d-flex justify-content-center" />
    </div>
    <!--Alt immagine-->
    <div class="row mt-2 g-0">
        <label for="imgalt" class="p-0 form-label">Scrivi una breve descrizione dell'immagine scelta:</label>
        <textarea required id="imgalt" name="imgalt" maxlength=50 
        placeholder="Descrizione immagine in meno di 50 caratteri" class="p-0 m-0"></textarea>
    </div>
    <!--Testo da inserire-->
    <div class="row mt-2 g-0">
        <label for="txtpost" class="p-0 form-label">Testo Post:</label>
        <textarea required id="txtpost" name="txtpost" maxlength=100 
        placeholder="Scrivi una descrizione per il tuo post di meno di 100 caratteri" class="p-0 m-0"></textarea>
    </div>
    <!--Per stilizzare i bottoni-->
    <div class="container">
        <div class="row w-100">
            <!--Animali presenti-->
            <label for="selectAnimals">Seleziona gli animali presenti in questo post</label>
            <p class="desktop-view">Tieni premuto control per selezionare multipli animali</p>
            <p class="mobile-view">Clicca per selezionare multipli animali</p>
            <select name="selectAnimals" id="selectAnimals" multiple="multiple" class="form-select" size=6>
                <?php
                if (isset($templateParams["animals"])) {
                    foreach ($templateParams["animals"] as $animal) {
                        echo "<option value=" . $animal . ">" . $animal . "</option>";
                    }
                }
                ?>
            </select>
            <!--Guarda post in anteprima-->
            <!--TODO: Da implementare-->
            <button class="btn btn-outline-primary col w-40"><img src="img/preview.svg" alt="">Guarda post in anteprima</button>
        </div>
    </div>
    <!--Gli errori, se presenti-->
    <?php
    if (isset($templateParams["error"])) {
        foreach ($templateParams["error"] as $error) {
            echo "<p class='text-danger'>" . $error . "</p>";
        }
    }
    ?>
    <!--Mostro gli animali, dinamicamente con js-->
    <div class="animal-display">
    </div>

    <!--Invia-->
    <div class="col text-center w-80 mt-5 p-10">
        <button type="submit" formmethod="POST" class="btn btn-outline-primary">
            <img src="img/publish.svg" alt=""> Pubblica il nuovo post</button>
    </div>
</form>
</div>
<script src="js/new-post.js" type="text/javascript"></script>