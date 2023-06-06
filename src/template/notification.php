<div class="list-group-item d-inline-flex notification" id=<?php echo "notification-" . $notification["id"] ?>>
    <a class="d-flex w-100 notification-text" href="<?php echo getNotificationRef($notification); ?>">
        <img src="<?php echo $thumbnail["src"]; ?>" alt="<?php echo $thumbnail["alt"]; ?>" class="miniature my-auto ms-0 me-3">
        <div class="my-auto">
            <div class="">
                <?php echo getNotificationMessage($notification); ?>
            </div>
            <div class="fw-lighter">
                <?php echo getNotificationDateTime($notification); ?>
            </div>
        </div>
    </a>
    <label for="<?php echo "btn-delete-" . $notification["id"] ?>" class="visually-hidden">Cancella notifica</label>
    <button type="button" class="btn btn-danger btn-delete-notification ms-auto col-xs-2 col-md-1 col-2" id=<?php echo "btn-delete-" . $notification["id"] ?>>
        <img src='<?php echo IMG_DIR . "trash-bin.svg" ?>' alt=""/>
    </button>
</div>