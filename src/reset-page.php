<h1>Stai cambiando la password per l'account <?php if (isset($templateParams["username"])) echo $templateParams["username"]; ?></h1>
<div class="list-group-item">
    <div class="row align-items-center">
        <div class="col">
            <label class="mb-0 fw-bolder" for="new-password">Nuova password</label>
            <p class="text-muted mb-0">Inserisci la nuova password.</p>
        </div>
        <div class="col-auto">
            <input type="password" id="new-password" name="new-password" autocomplete="new-password" />
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
            <input type="password" id="new-password-repeat" name="new-password-repeat" autocomplete="new-password" />
        </div>
    </div>
</div>

<!-- Submit button -->
<label>Ti manderemo una mail per resettare la password legata all'account scelto</label>
<button type="submit" class="btn btn-primary btn-block mb-4">Invia mail di reset password</button>
