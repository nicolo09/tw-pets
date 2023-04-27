<section class="text-center text-lg-start">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="card-body text-center">
                    <h2 class="fw-bold">Esegui l'accesso</h2>
                    <form action="login.php" method="post">
                        <!-- Username input -->
                        <div class="form-outline">
                            <label class="form-label" for="usernameTextBox">Nome Utente o Email</label>
                            <input type="text" placeholder="Username / Email" class="form-control" id="usernameTextBox" name="username" />
                        </div>

                        <!-- Password input -->
                        <div class="form-outline">
                            <label class="form-label" for="passwordTextBox">Password</label>
                            <input type="password" placeholder="Password" class="form-control" id="passwordTextBox" name="password" />
                            <a href="reset-password.php">Password dimenticata?</a>
                        </div>

                        <!-- Remember me checkbox
                        <div class="form-outline">
                            <input type="checkbox" class="form-check-input" id="rememberCheckBox" name="rememberMe">
                            <label class="form-check-label" for="rememberCheckBox">Ricordami</label>
                        </div>  -->

                        <!-- Submit button -->
                        <button type="submit" class="btn btn-primary btn-block mb-4">Login</button>

                        <?php
                        if (isset($templateParams["errors"])) {
                            foreach ($templateParams["errors"] as $error) {
                                echo "<p class='text-danger'>" . $error . "</p>";
                            }
                        }
                        ?>

                        <!-- Go to register form -->
                        <div>
                            <label>Non hai ancora un account? - </label>
                            <a href="register.php"> Registrati ora! </a>
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