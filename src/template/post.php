<!--Rettangolo-->
<div class="card mx-auto col-12 col-lg-10 col-xl-8 border-black align-items-center justify-content-center" id="post-card-<?php if (isset($templateParams["id"])) {
                                                                                                                                echo $templateParams["id"];
                                                                                                                            } ?>">
    <div class="row border-bottom border-black post-header">
        <!--Utente che ha postato-->
        <div class="col p-0">
            <img class="pro-pic text-center img-fluid" src=<?php if (isset($templateParams["immagineprofilo"])) {
                                                                echo $templateParams["immagineprofilo"];
                                                            } else {
                                                                echo IMG_DIR . "default.jpg";
                                                            } ?> alt=<?php if (isset($templateParams["username"])) {
                                                                                    echo "Foto profilo di " . $templateParams["username"];
                                                                                } else {
                                                                                    echo "Foto profilo di utente non esistente";
                                                                                } ?>>
        </div>
        <a class="col d-flex align-items-center" <?php if (isset($templateParams["username"])) {
                                                        echo html_entity_decode('href=view-user-profile.php?username=' . $templateParams["username"] . '&type=person');
                                                    } else {
                                                        echo html_entity_decode('href=#');
                                                    } ?>><?php if (isset($templateParams["username"])) {
                                                                    echo $templateParams["username"];
                                                                } else {
                                                                    echo "Utente non esiste";
                                                                } ?></a>
    </div>
    <!--Immagine-->
    <img class="post-image w-100 pt-1 pb-1" src=<?php if (isset($templateParams["immagine"])) {
                                                    echo $templateParams["immagine"];
                                                } else {
                                                    echo "#";
                                                } ?> alt=<?php if (isset($templateParams["alt"])) {
                                                                echo $templateParams["alt"];
                                                            } else {
                                                                echo "Alt non presente";
                                                            } ?>>
    <div class="w-100 m-0 border-bottom border-top border-black d-flex justify-content-center g-0">
        <!--Tasti-->
        <div class="row w-50 g-0">
            <button class="btn btn-outline btn-outline-primary col" id="likeB-<?php if (isset($templateParams["id"])) {
                                                                                    echo $templateParams["id"];
                                                                                } ?>">
        </div>
        <div class="row w-50 g-0">
            <button class="btn btn-outline btn-outline-primary col" id="saveB-<?php if (isset($templateParams["id"])) {
                                                                                    echo $templateParams["id"];
                                                                                } ?>">
        </div>
    </div>
    <!--Error messages are printed here-->
    <div class="error">
    </div>
    <div class="text-left">
        <p>
            <?php if (isset($templateParams["descrizione"]) && isset($templateParams["username"])) {
                $tmp = $templateParams["username"] . ":" . $templateParams["descrizione"];
                echo $tmp;
                //TODO:Animali che ci sono
            } ?>
        </p>
    </div>
</div>
<script src="js/post.js" type="text/javascript"></script>