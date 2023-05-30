<div class="card mx-auto col-12 col-sm-8 col-md-6 col-lg-5 col-xl-4 g-0 text-center d-flex justify-content-center border-0">
    <h1>Crea nuovo post</h1>
    <form action="#" method="POST" class="" enctype="multipart/form-data">
        <!--Immagine mostrata qui-->
        <div>
            <img id="imgPreview" src="#" alt="Immagine inserita da utente" class=" w-100">
        </div>
        <!--Immagine da inserire-->
        <div class="row mt-2 g-0 d-flex justify-content-center">
            <label for="imgpostinput" class="form-label">Scegli l'immagine del post</label>
            <input required type="file" name="imgpost" id="imgpostinput" accept="image/jpg, image/jpeg, image/png, image/gif" class="form-control w-50 g-0 d-flex justify-content-center" />
        </div>
        <!--Alt immagine-->
        <div class="row mt-2 g-0">
            <label for="imgalt" class="p-0 form-label">Scrivi una breve descrizione dell'immagine scelta:</label>
            <textarea required id="imgalt" name="imgalt" maxlength=50 placeholder="Descrizione immagine in meno di 50 caratteri" class="p-0 m-0"></textarea>
        </div>
        <!--Testo da inserire-->
        <div class="row mt-2 g-0">
            <label for="txtpost" class="p-0 form-label">Testo Post:</label>
            <textarea required id="txtpost" name="txtpost" maxlength=200 placeholder="Scrivi una descrizione per il tuo post di meno di 200 caratteri" class="p-0 m-0"></textarea>
        </div>
        <!--Per stilizzare i bottoni-->
        <div class="w-100">
            <!--Animali presenti-->
            <label for="selectAnimals">Seleziona gli animali presenti in questo post</label>
            <select name="selectAnimals[]" id="selectAnimals" multiple="multiple" class="form-select selectAnimals w-100 p-0" size=6>
                <?php
                if (
                    isset($templateParams["animals"]) && isset($templateParams["animalsImg"])
                    && count($templateParams["animals"]) == count($templateParams["animalsImg"])
                ) {
                    for ($i = 0; $i < count($templateParams["animals"]); $i++) {
                        echo "<option value=" . $templateParams["animals"][$i] . " data-img=" . $templateParams["animalsImg"][$i] . ">" . $templateParams["animals"][$i] . "</option>";
                    }
                }
                ?>
            </select>
        </div>
        <!--Gli errori, se presenti-->
        <?php
        if (isset($templateParams["error"])) {
            foreach ($templateParams["error"] as $error) {
                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert"> <label class="top-page-popup">' . $error . '</label> <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            }
        } else {
            if (isset($templateParams["success"])) {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert"> <label class="top-page-popup">' . $templateParams["success"] . '</label> <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            }
        }
        ?>
        <!--Mostro gli animali, dinamicamente con js-->
        <div class="animal-display">
        </div>

        <!--Guarda post in anteprima-->
        <button class="btn btn-outline-primary col w-40 mt-4" id="preview" type="button"><img src="img/preview.svg" alt="">Guarda post in anteprima</button>

        <!--Invia-->
        <div class="col text-center w-80 mt-5 p-10">
            <button type="submit" formmethod="POST" class="btn btn-outline-primary">
                <img src="img/publish.svg" alt="" /> Pubblica il nuovo post
            </button>
        </div>
    </form>
</div>
<script src="js/new-post.js" type="text/javascript"></script>