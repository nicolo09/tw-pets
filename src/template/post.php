<!--Rettangolo-->
<div class="card mx-auto col-12 col-lg-10 col-xl-8 border-black align-items-center justify-content-center">
    <div class="row w-100 border-bottom border-black b-0 m-0 p-2">
        <!--Utente che ha postato-->
        <img class="proPic p-0 text-center" src=<?php if (isset($templateParams["immagineprofilo"])) {
                                                    echo $templateParams["immagineprofilo"];
                                                } else {
                                                    echo IMG_DIR . "default.jpg";
                                                } ?> alt=<?php if (isset($templateParams["username"])) {
                                                                echo "Foto profilo di " . $templateParams["username"];
                                                            } else {
                                                                echo "Foto profilo di utente non esistente";
                                                            } ?>>
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
            <button class="btn btn-outline btn-outline-primary col <?php if (isset($templateParams["nlikes"]) == false || $templateParams["nlikes"] < 0) {
                                                                        echo "disabled";
                                                                    } ?>">
                <?php if (isset($templateParams["nlikes"]) && $templateParams["nlikes"] > 0) {
                    if (isset($templateParams["liked"]) && $templateParams["liked"] == true) {
                        //Post con mi piace
                        echo html_entity_decode('<img class="w-25" src=' . IMG_DIR . "thumb_up_filled.svg" . ' alt="">' . $templateParams["nlikes"] . ' Mi Piace</button>');
                    } else {
                        //Post senza mi piace
                        echo html_entity_decode('<img class="w-25" src=' . IMG_DIR . "thumb_up.svg" . ' alt="">' . $templateParams["nlikes"] . ' Mi Piace</button>');
                    }
                } else {
                    echo html_entity_decode('<img class="w-25" src=' . IMG_DIR . "thumb_up.svg" . ' alt=""> Impossibile mettere mi piace </button>');
                }
                ?>
        </div>
        <div class="row w-50 g-0">
            <button class="btn btn-outline btn-outline-primary col <?php if (isset($templateParams["nlikes"]) == false || $templateParams["nlikes"] < 0) {
                                                                        echo "disabled";
                                                                    } ?>">
                <?php if (isset($templateParams["nlikes"]) && $templateParams["nlikes"] > 0) {
                    if (isset($templateParams["saved"]) && $templateParams["saved"] == true) {
                        //Post salvato
                        echo html_entity_decode('<img class="w-25" src=' . IMG_DIR . "star_filled.svg" . ' alt=""> Salvato </button>');
                    } else {
                        //Post non salvato
                        echo html_entity_decode('<img class="w-25" src=' . IMG_DIR . "star.svg" . ' alt=""> Salva </button>');
                    }
                } else {
                    echo html_entity_decode('<img class="w-25" src=' . IMG_DIR . "star.svg" . ' alt=""> Impossibile salvare il post </button>');
                }
                ?>
        </div>
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