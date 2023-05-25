<div class="container">
    <div class="d-grid gap-3" id="buttons-div">
        <button class="btn btn-primary"><img src="img/person.svg"><?php echo $_SESSION['username']; ?></button>
        <div class="row">
            <div class="col text-center"><button class="btn btn-secondary profile-button row" id="view-profile-button"><img src="img/person.svg">Visualizza profilo</button></div>
            <div class="col text-center"><button class="btn btn-secondary profile-button row" id="notifications-button"><img src="img/notifications.svg">Notifiche</button></div>
        </div>
        <div class="row">
            <div class="col text-center"><button class="btn btn-secondary profile-button row" id="add-post-button"><img src="img/post_add.svg">Aggiungi post</button></div>
            <div class="col text-center"><button class="btn btn-secondary profile-button row" id="animals-button"><img src="img/pets.svg">Animali</button></div>
        </div>
        <div class="row">
            <div class="col text-center"><button class="btn btn-secondary profile-button row"><img src="img/saved_posts.svg">Post salvati</button></div>
        <div class="col text-center"><button class="btn btn-secondary profile-button row" id="settings-button"><img src="img/settings.svg">Impostazioni</button></div>
    </div>
    <button class="btn btn-danger" id="logout-button"><img src="img/logout.svg">Logout</button>
    </div>
</div>
<script src="js/my-profile.js"></script>