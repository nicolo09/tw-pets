<section class="text-center text-lg-start">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="card-body text-center">
                    <h2 class="fw-bold">Hai dimenticato la password?</h2>
                    <form action="reset-password.php" method="post">
                        
                        <!-- Username input -->
                        <div class="form-outline">
                            <label class="form-label" for="usernameTextBox">Inserisci nome Utente o Email</label>
                            <input type="text" placeholder="Username / Email" class="form-control" id="usernameTextBox" name="username" autocomplete="username" />
                        </div>

                        <!-- Submit button -->
                        <label>Ti manderemo una mail per resettare la password legata all'account scelto</label>
                        <button type="submit" class="btn btn-primary btn-block mb-4">Invia mail di reset password</button>

                        <!-- Go to register form -->
                        <div>
                            <label>Non hai ancora un account? - </label>
                            <a href="register.php"> Registrati ora! </a>
                        </div>
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