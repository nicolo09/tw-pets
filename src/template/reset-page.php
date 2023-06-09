<section class="text-center text-lg-start">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="card-body text-center">
                    <h2 class="fw-bold">Inserisci la nuova password per l'account di <?php if (isset($templateParams["username"])) echo $templateParams["username"]; ?></h2>
                    <form action="#" method="post">

                        <!-- Password input -->
                        <p class="row align-items-center">Scegli una password sicura: deve essere lunga almeno 6 caratteri, contenere una maiuscola, minuscola, un numero e un carattere speciale.</p>
                        <div class="form-outline">
                            <label class="mb-0 fw-bolder" for="new-password">Nuova password</label>
                            <p class="text-muted mb-0">Inserisci la nuova password.</p>
                            <input class="w-100" type="password" id="new-password" name="new-password" autocomplete="new-password" required/>
                        </div>
                        <div class="form-outline">
                            <label class="mb-0 fw-bolder" for="new-password-repeat">Conferma nuova password</label>
                            <p class="text-muted mb-0">Inserisci di nuovo la nuova password.</p>
                            <input class="w-100" type="password" id="new-password-repeat" name="new-password-repeat" autocomplete="new-password" required/>
                        </div>
                        <!-- Submit button -->
                        <button type="submit" class="btn btn-primary btn-block mt-2">Cambia password</button>
                    </form>
                </div>
            </div>
            <div class="col-lg-6">
                <img class="desktop-view w-100" src="img/site-image.jpg" alt="Image of pets"/>
            </div>
        </div>
    </div>
</section>
