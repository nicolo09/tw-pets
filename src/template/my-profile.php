<div class="d-grid gap-3" id="buttons-div">
    <button class="btn btn-primary"><img src="img/person.svg"><?php echo $_SESSION['username']; ?></button>
    <div class="row">
        <div class="col text-center"><button class="btn btn-secondary row" id="view-profile-button"><img src="img/person.svg">Visualizza profilo</button></div>
        <div class="col text-center"><button class="btn btn-secondary row" id="notifications-button"><img src="img/notifications.svg">Notifiche</button></div>
    </div>
    <div class="row">
        <div class="col text-center"><button class="btn btn-secondary row" id="add-post-button"><img src="img/post_add.svg">Aggiungi post</button></div>
        <div class="col text-center"><button class="btn btn-secondary row" id="animals-button"><img src="img/pets.svg">Animali</button></div>
    </div>
    <div class="row">
        <div class="col text-center"><button class="btn btn-secondary row"><img src="img/extension.svg">Tasto bonus</button></div>
        <div class="col text-center"><button class="btn btn-secondary row" id="settings-button"><img src="img/settings.svg">Impostazioni</button></div>
    </div>
    <button class="btn btn-danger" id="logout-button"><img src="img/logout.svg">Logout</button>
</div>
<script src="js/my-profile.js"></script>