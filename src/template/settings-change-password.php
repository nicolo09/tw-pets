<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-8 mx-auto">
            <h2 class="h3 mb-4 page-title text-center">Impostazioni</h2>
            <h3 class="mb-0 mt-5">Cambia password</h3>
            <p>Riempi i campi e premi conferma per cambiare la password del tuo account.</p>
            <form action="change-password.php" method="POST">
                <div class="setting-section">
                    <div class="list-group mb-5 shadow">
                        <div class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col">
                                    <label class="mb-0 fw-bolder" for="old-password">Vecchia password</label>
                                    <p class="text-muted mb-0">Inserisci la tua password corrente.</p>
                                </div>
                                <div class="col-auto">
                                    <input type="password" id="old-password" name="old-password" />
                                </div>
                            </div>
                        </div>
                        <div class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col">
                                    <label class="mb-0 fw-bolder" for="new-password">Nuova password</label>
                                    <p class="text-muted mb-0">Inserisci la nuova password.</p>
                                </div>
                                <div class="col-auto">
                                    <input type="password" id="new-password" name="new-password" />
                                </div>
                            </div>
                        </div>
                        <div class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col">
                                    <label class="mb-0 fw-bolder" for="new-password-repeat">Ripeti nuova password</label>
                                    <p class="text-muted mb-0">Inserisci di nuovo la nuova password.</p>
                                </div>
                                <div class="col-auto">
                                    <input type="password" id="new-password-repeat" name="new-password-repeat" />
                                </div>
                            </div>
                        </div>
                        <?php if (isset($templateParams["error"])){
                            echo "<div class='list-group-item'>";
                            echo "<div class='row align-items-center'>";
                            echo "<p class='text-danger mb-0'>".$templateParams["error"]."</p>";
                            echo "</div>";
                            echo "</div>";
                        } ?>
                        <div class="list-group-item">
                            <div class="row align-items-center">
                                <button type="submit" class="btn btn-primary settings-item">Conferma</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script src=/js/settings.js></script>