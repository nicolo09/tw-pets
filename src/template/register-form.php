<section class="text-center text-lg-start">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="card-body text-center">
                    <h2 class="fw-bold">Iscriviti adesso</h2>
                    <form action="register.php" method="post">
                        <!-- Username input -->
                        <div class="form-outline">
                            <label class="form-label">Nome Utente</label>
                            <input type="text" placeholder="username" class="form-control" name="username"/>
                        </div>

                        <!-- Email input -->
                        <div class="form-outline">
                            <label class="form-label">Indirizzo Email</label>
                            <input type="email" placeholder="esempio@dominio.com" class="form-control" name="email"/>
                        </div>

                        <!-- Password input -->
                        <div class="form-outline">
                            <label class="form-label">Password</label>
                            <input type="password" placeholder="Inserire la password" class="form-control" name="password"/>
                        </div>

                        <!-- Password confirm -->
                        <div class="form-outline">
                            <label class="form-label">Conferma Password</label>
                            <input type="password" placeholder="Ripetere la password" class="form-control" name="confirm_password"/>
                        </div>

                        <!-- Submit button -->
                        <button type="submit" class="btn btn-primary btn-block mb-4">Registrati</button>

                        <?php
                        if (isset($templateParams["errors"])) {
                            foreach ($templateParams["errors"] as $error) {
                                echo "<p class='text-danger'>" . $error . "</p>";
                            }
                        }
                        ?>

                        <!-- Go to login form -->
                        <div>
                            <label>Hai già un account? - </label>
                            <a href="login.php"> Esegui il login </a>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-6">
                <h1 class="text-center">Placeholder immagine</h1>
            </div>
        </div>
    </div>
</section>
