<div class="row justify-content-center">
    <?php 
        if (count($notifications) == 0) {
            echo "<h1 class='col-12 text-center'>Non ci sono notifiche</h1>";
        }
        else {
            echo "<h1 class='col-12 text-center'>Notifiche</h1>";
        }
    ?>
    <div class="list-group col-12 col-lg-9 col-xl-7 mx-auto">
        <?php foreach ($notifications as $notification) :
            $thumbnail = getNotificationThumbnail($notification); ?>
            <a class="list-group-item" href=<?php echo getNotificationRef($notification); ?>>
                <img src=<?php echo $thumbnail["src"]; ?> alt=<?php echo $thumbnail["alt"]; ?> class="col-1 miniature">
                <?php echo getNotificationMessage($notification); ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>