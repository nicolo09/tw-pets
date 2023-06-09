<section class="text-center text-lg-start">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="card-body text-center">
                    <h2 class="fw-bold">Iscriviti adesso</h2>
                    <form action="register.php" method="post">
                        <!-- Username input -->
                        <div class="form-outline">
                            <label class="form-label" for="usernameID">Nome Utente</label>
                            <input type="text" placeholder="username" class="form-control" name="username" autocomplete="username" maxlength="25" id="usernameID"/>
                        </div>

                        <!-- Email input -->
                        <div class="form-outline">
                            <label class="form-label" for="emailID">Indirizzo Email</label>
                            <input type="email" placeholder="esempio@dominio.com" class="form-control" name="email" autocomplete="username" id="emailID" />
                        </div>

                        
                        <!-- Password input -->
                        <div class="form-outline">
                            <label class="form-label" for="passwordID">Password</label>
                            <input type="password" placeholder="Inserire la password" class="form-control" name="password" autocomplete="new-password" id="passwordID" />
                        </div>
                        
                        <!-- Password confirm -->
                        <div class="form-outline">
                            <label class="form-label" for="passwordRepeatID">Conferma Password</label>
                            <input type="password" placeholder="Ripetere la password" class="form-control" name="confirm_password" autocomplete="new-password" id="passwordRepeatID" />
                        </div>
                        
                        <p>Scegli una password sicura: deve essere lunga almeno 6 caratteri, contenere una maiuscola, minuscola, un numero e un carattere speciale.</p>
                        <!-- Submit button -->
                        <button type="submit" class="btn btn-primary btn-block mb-4">Registrati</button>

                        <!-- Go to login form -->
                        <div>
                            <label>Hai già un account? - </label>
                            <a href="login.php"> Esegui il login </a>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-6">
                <img class="desktop-view w-100" src="../img/site-image.jpg" alt="Image of pets"/>
            </div>
        </div>
    </div>
</section>
