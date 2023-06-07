<div class="container">
    <div class="row justify-content-center mb-5">
        <?php
        if (count($notifications) == 0) {
            echo "<h1 class='col-12 text-center' id='title'>Non ci sono notifiche</h1>";
        } else {
            echo "<h1 class='col-12 text-center' id='title'>Notifiche</h1>";
        }
        ?>
        <div class="list-group col-12 col-lg-9 col-xl-7 p-1" id="notifications-list">
            <?php foreach ($notifications as $notification) :
                $thumbnail = getNotificationThumbnail($notification, $dbh);
                require("template/notification.php"); ?>
            <?php endforeach; ?>
        </div>
        <div class="d-flex justify-content-center align-items-center mt-4">
            <div class="spinner-border text-primary spinner-border-sm" id="spinner" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-9 col-xl-7 p-0 fixed-bottom mx-auto mb-1 px-1">
        <button type="button" class="btn btn-danger btn-delete-all-notifications w-100" id="btn-delete-all-notifications" <?php echo count($notifications) == 0 ? "disabled" : "" ?>>
            Cancella tutte
        </button>
    </div>
</div>
<script src="js/notifications.js"></script>