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
            <h2 class="fw-bold"><?php echo $templateParams["subtitle"] ?></h2>
            <form action="profile-manage-animal.php<?php if(isset($templateParams["animal"])) {echo "?animal=" . $templateParams["animal"];}?>" method="post" enctype="multipart/form-data">

                <!-- Image input (optional) -->
                <div class="form-outline">
                    <label class="form-label fs-3 w-100" for="imgprofile">Immagine di profilo</label>
                    <div class="w-100"><img class="proPic" id="imgPreview" src="<?php echo $templateParams["img"]?>" alt="Current profile picture" /></div>
                    <input class="center-align pt-2" type="file" accept=".png,.jpg,.jpeg" onchange="imagePreview(this)" id="imgprofile" name="imgprofile"/>
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
                    <textarea placeholder="Max 100 caratteri" class="form-control" maxlength="100" id="descriptionTextArea" name="description"><?php if(isset($templateParams["description"])) { echo $templateParams["description"]; }?></textarea>
                </div>

                <!-- TODO others owner input -->
                <div class="form-outline">
                    <label class="form-label w-100 pb-2" for="multiSelector">Padroni selezionati:</label>
                    <?php if(!empty($templateParams["mutuals"])):?>
    
                    <select class="form-select" id="multiSelector" name="owners[]" multiple="multiple" size=6>
                    <?php
                    foreach($templateParams["mutuals"] as $mutual) {
                        
                        $active = isset($templateParams["owners"]) 
                            && in_array($mutual, $templateParams["owners"]) 
                            ? " selected"
                            : "";
                        
                        echo "<option value="
                        . $mutual["username"] 
                        . " data-img=" 
                        . IMG_DIR . $mutual["immagine"]
                        . $active
                        .">" 
                        . $mutual["username"] 
                        . "</option>";
                    }
                    ?>
                    <option value="Pappero">Pappero1</option>
                    </select>
                    
                    <?php else : ?>
                    <label class="text-decoration-underline fw-bold">Al momento non ci sono utenti che puoi aggiungere come padroni</label>
                    <?php endif ?>
                    <label class="w-100 pt-2">Solo gli utenti che segui e ti seguono possono essere aggiunti.</label>
                </div>

                <!-- Submit button -->
                <button type="submit" class="btn btn-primary btn-block mb-4">Conferma</button>
                
            </form>
        </div>
    </div>
</section>
<script src="js/form-utils.js"></script>