<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-8 mx-auto">
            <h2 class="h3 mb-4 page-title text-center">Impostazioni</h2>
            <h3 class="mb-0 mt-5">Cambia email</h3>
            <p>Riempi i campi e premi conferma per cambiare l'email associata al tuo account.</p>
            <form action="change-email.php" method="POST">
                <div class="setting-section">
                    <div class="list-group mb-5 shadow">
                        <div class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col">
                                    <label class="mb-0 fw-bolder" for="old-email">Vecchia email</label>
                                    <p class="text-muted mb-0">Inserisci la mail attualmente associata all'account.</p>
                                </div>
                                <div class="col-auto">
                                    <input type="email" id="old-email" name="old-email" autocomplete="current-email" />
                                </div>
                            </div>
                        </div>
                        <div class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col">
                                    <label class="mb-0 fw-bolder" for="new-email">Nuova email</label>
                                    <p class="text-muted mb-0">Inserisci la nuova mail.</p>
                                </div>
                                <div class="col-auto">
                                    <input type="email" id="new-email" name="new-email" autocomplete="new-email" />
                                </div>
                            </div>
                        </div>
                        <div class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col">
                                    <label class="mb-0 fw-bolder" for="new-email-repeat">Ripeti nuova email</label>
                                    <p class="text-muted mb-0">Inserisci di nuovo la nuova mail.</p>
                                </div>
                                <div class="col-auto">
                                    <input type="email" id="new-email-repeat" name="new-email-repeat" autocomplete="new-email" />
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