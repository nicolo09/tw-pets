<section class="text-center text-lg-start">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="card-body text-center">
                    <h2 class="fw-bold">Inserisci la nuova password per l'account di <?php if (isset($templateParams["username"])) echo $templateParams["username"]; ?></h2>
                    <form action="#" method="post">

                        <!-- Password input -->
                        <div class="row align-items-center">
                            <div class="col">
                                <label class="mb-0 fw-bolder" for="new-password">Nuova password</label>
                                <p class="text-muted mb-0">Inserisci la nuova password.</p>
                            </div>
                            <div class="col-auto">
                                <input type="password" id="new-password" name="new-password" autocomplete="new-password" required/>
                            </div>
                        </div>
                        <div class="row align-items-center">
                            <div class="col">
                                <label class="mb-0 fw-bolder" for="new-password-repeat">Ripeti nuova password</label>
                                <p class="text-muted mb-0">Inserisci di nuovo la nuova password.</p>
                            </div>
                            <div class="col-auto">
                                <input type="password" id="new-password-repeat" name="new-password-repeat" autocomplete="new-password" required/>
                            </div>
                        </div>

                        <!-- Submit button -->
                        <button type="submit" class="btn btn-primary btn-block mb-4">Cambia password</button>
                    </form>
                </div>
            </div>
            <div class="col-lg-6">
                <img class="desktop-view w-100" src="img/site-image.jpg" alt="Image of pets"/>
            </div>
        </div>
    </div>
</section>
