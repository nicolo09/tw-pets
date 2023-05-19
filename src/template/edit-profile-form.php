<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-8 mx-auto">
            <div class="card-body text-center">
                <h2 class="fw-bold"><?php echo $templateParams["subtitle"] ?></h2>
                <form action="edit-profile.php?action" method="post" enctype="multipart/form-data">

                    <!-- Image input (optional) -->
                    <div class="form-outline">
                        <label class="form-label fs-3 w-100" for="imgprofile">Immagine di profilo</label>
                        <div class="w-100 mb-4">
                            <img class="proPic" title="Cambia immagine" id="imgPreview" src="<?php echo $templateParams["img"] ?>" alt="Current profile picture" />
                        </div> 
                        <input class="form-control" type="file" accept=".png,.jpg,.jpeg" id="imgprofile" name="imgprofile" />
                    </div>


                    <!-- Username input -->
                    <div class="form-outline">
                        <label class="form-label" for="usernameTextBox">Nome utente</label>
                        <input type="text" placeholder="Username" value="<?php echo $templateParams["username"];  ?>" class="form-control" maxlength="25" id="usernameTextBox" name="username" disabled/>
                    </div>

                    <!-- Animal type input -->
                    <div class="form-outline">
                        <label class="form-label" for="employmentTextBox">Impiego (opzionale)</label>
                        <input type="text" placeholder="Max 50 caratteri" value="<?php  echo $templateParams["employment"]; ?>" class="form-control" maxlength="50" id="employmentTextBox" name="employment" />
                    </div>

                    <!-- Description (optional) -->
                    <div class="form-outline">
                        <label class="form-label" for="descriptionTextArea">Descrizione (opzionale)</label>
                        <textarea placeholder="Max 200 caratteri" class="form-control" maxlength="200" id="descriptionTextArea" name="description"><?php echo $templateParams["description"]; ?></textarea>
                    </div>

                    <!-- Submit button -->
                    <button type="submit" class="btn btn-primary btn-block mb-4">Conferma</button>

                </form>
            </div>
        </div>
    </div>
</div>
<script src="js/form-utils.js"></script>
