<div class="container">
    <div class="d-grid gap-3" id="buttons-div">
        <button class="btn btn-primary" id="modify-account-button" title="Modifica il tuo profilo"><img src="<?php echo getUserProfilePic($templateParams["user"], $dbh)?>" class="miniature"><span class="ms-3 short-text"><?php echo $templateParams["user"]; ?></span></button>
        <div class="row profile-row">
            <div class="col text-center"><button class="btn btn-secondary user-button" id="view-profile-button"><img src="img/person.svg" class="w-100">Visualizza profilo</button></div>
            <div class="col text-center"><button class="btn btn-secondary user-button" id="notifications-button"><img src="img/notifications.svg" class="w-100">Notifiche</button></div>
        </div>
        <div class="row profile-row">
            <div class="col text-center"><button class="btn btn-secondary user-button" id="add-post-button"><img src="img/post_add.svg" class="w-100">Aggiungi post</button></div>
            <div class="col text-center"><button class="btn btn-secondary user-button" id="animals-button"><img src="img/pets.svg" class="w-100">Animali</button></div>
        </div>
        <div class="row profile-row">
            <div class="col text-center"><button class="btn btn-secondary user-button" id="saved-posts-button"><img src="img/saved_posts.svg" class="w-100">Post salvati</button></div>
        <div class="col text-center"><button class="btn btn-secondary user-button" id="settings-button"><img src="img/settings.svg" class="w-100">Impostazioni</button></div>
    </div>
    <button class="btn btn-danger" id="logout-button"><img src="img/logout.svg">Logout</button>
    </div>
</div>
<script src="js/my-profile.js"></script>