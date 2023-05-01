<?php 
if(isset($templateParams["errors"])) {
    foreach ($templateParams["errors"] as $error) {
        echo "<p class='text-danger'>" . $error . "</p>";
    }
}
?>

<section class="text-center text-lg-start">
    <div class="container">
        <div class="card-body text-center">
            <h2 class="fw-bold">Aggiungi un nuovo animale</h2>
            <form action="profile-add-animal.php<?php if(isset($templateParams["animal"])) {echo "?animal=" . $templateParams["animal"];}?>" method="post" enctype="multipart/form-data">

                <!-- Image input (optional) -->
                <div class="form-outline">
                    <h4 class="form-label w-100" for="imgprofile">Immagine di profilo</h4>
                    <img class="proPic" id="imgPreview" src="<?php echo $templateParams["img"]?>" alt="Current profile picture" />
                    <input class="center-align" type="file" accept=".png,.jpg,.jpeg" onchange="imagePreview(this)" id="imgprofile" name="imgprofile"/>
                </div>


                <!-- Username input -->
                <div class="form-outline">
                    <label class="form-label" for="usernameTextBox">Nome utente del tuo animale</label>
                    <input type="text" placeholder="Username" <?php if(isset($templateParams["animal"])) { echo "value=\"" . $templateParams["animal"] . "\" disabled"; }?> class="form-control" maxlength="25" id="usernameTextBox" name="username"/>
                </div>

                <!-- Animal type input -->
                <div class="form-outline">
                    <label class="form-label" for="typeTextBox">Tipo di animale</label>
                    <input type="text" placeholder="Esempio: Labrador, Gatto persiano..." <?php if(isset($templateParams["type"])) { echo "value=\"" . $templateParams["type"] . "\""; } ?> class="form-control" maxlength="30" id="typeTextBox" name="type"/>
                </div>

                <!-- Description (optional) -->
                <div class="form-outline">
                    <label class="form-label" for="descriptionTextArea">Descrizione (opzionale)</label>
                    <textarea placeholder="Max 100 caratteri" <?php if(isset($templateParams["description"])) { echo "value=\"" . $templateParams["description"] . "\"";}?> class="form-control" maxlength="100" id="descriptionTextArea" name="description"></textarea>
                </div>

                <!-- TODO others owner input -->

                <!-- Submit button -->
                <button type="submit" class="btn btn-primary btn-block mb-4">Conferma</button>
                
            </form>
        </div>
    </div>
</section>