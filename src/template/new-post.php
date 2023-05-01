<h1>Crea nuovo post</h1>
<form action="#" method="POST" class="w-100">
    <!--Immagine mostrata qui-->
    <div>
        <img id="imgPreview" src="" alt="Immagine inserita da utente" class="w-100">
    </div>
    <!--Immagine da inserire-->
    <div class="row mt-2">
        <label for="imgpostinput">Scegli l'immagine del post</label>
        <input required type="file" name="imgpost" id="imgpostinput" accept="jpg, jpeg, png, gif">
    </div>
    <!--Alt immagine-->
    <div class="row mt-2 my-son-textarea">
        <label for="imgalt" class="p-0">Scrivi una breve descrizione dell'immagine scelta:</label>
        <textarea required id="imgalt" name="imgalt" maxlenght=50 
        placeholder="Descrizione immagine in meno di 50 caratteri" class="p-0 m-0"></textarea>
    </div>
    <!--Testo da inserire-->
    <div class="row mt-2 my-son-textarea">
        <label for="txtpost" class="p-0">Testo Post:</label>
        <textarea required id="txtpost" name="txtpost" maxlenght=100 
        placeholder="Scrivi una descrizione per il tuo post di meno di 100 caratteri" class="p-0 m-0"></textarea>
    </div>
    <!--Per stilizzare i bottoni-->
    <div class="container">
        <div class="row w-100">
            <!--Animali presenti-->
            <label for="selectAnimals">Seleziona gli animali presenti in questo post</label>
            <select name="selectAnimals" id="selectAnimals" multiple="multiple">
            <?php
            if (isset($templateParams["animals"])) {
                foreach ($templateParams["animals"] as $animal) {
                    echo "<option value=". $animal .">" . $animal . "</option>";
                }
            }
            ?> 
            </select>
            <!--Guarda post in anteprima-->
            <button class="btn btn-outline-primary col w-40"><img src="img/preview.svg" alt=""><a href="preview-post.php">Guarda post in anteprima</a></button>
        </div>
    </div>
    <!--Gli errori, se presenti-->
    <?php
    if (isset($templateParams["error"])) {
        echo "<p class='text-danger'>" . $templateParams["error"] . "</p>";
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
<script src="js/new-post.js" type="text/javascript"></script>