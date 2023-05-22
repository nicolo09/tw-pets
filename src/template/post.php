<!--Rettangolo-->
<div class="card mx-auto col-12 col-sm-8 col-md-6 col-lg-5 col-xl-4 border-black align-items-center justify-content-center post" id="post-card-<?php if (isset($templateParams["id"])) {
                                                                                                                                                    echo $templateParams["id"];
                                                                                                                                                } ?>">
    <div class="card-header row border-bottom border-black post-header p-1">
        <!--Utente che ha postato-->
        <div class="col p-0">
            <img class="pro-pic text-center img-fluid" src=<?php if (isset($templateParams["immagineprofilo"])) {
                                                                echo $templateParams["immagineprofilo"];
                                                            } else {
                                                                echo IMG_DIR . "default.jpg";
                                                            } ?> alt="<?php if (isset($templateParams["username"])) {
                                                                            echo "Foto profilo di " . $templateParams["username"];
                                                                        } else {
                                                                            echo "Foto profilo di utente non esistente";
                                                                        } ?>">
            <a class="align-items-center" <?php if (isset($templateParams["username"])) {
                                                echo html_entity_decode('href="' . getUserProfileHref($templateParams["username"]) . '"');
                                            } else {
                                                echo html_entity_decode('href=#');
                                            } ?>><?php if (isset($templateParams["username"])) {
                                                        echo $templateParams["username"];
                                                    } else {
                                                        echo "Utente non esiste";
                                                    } ?></a>
        </div>
    </div>
    <!--Immagine-->
    <img class="w-100" src=<?php if (isset($templateParams["immagine"])) {
                                echo $templateParams["immagine"];
                            } else {
                                echo "#";
                            } ?> alt="<?php if (isset($templateParams["alt"])) {
                                            echo $templateParams["alt"];
                                        } else {
                                            echo "Alt non presente";
                                        } ?>">
    <div class="card-footer w-100 p-0 m-0">
        <div class="w-100 m-0 d-flex justify-content-center row">
            <!--Tasti-->
            <div class="col div-button-post">
                <button class="btn btn-outline btn-outline-primary button-post align-middle" id="like-post-card-<?php if (isset($templateParams["id"])) {
                                                                                                                    echo $templateParams["id"];
                                                                                                                } ?>" <?php if (isset($templateParams["disableLike"]) && $templateParams["disableLike"] == true) {
                                                                                                                            echo "disabled";
                                                                                                                        } ?>>
                </button>

            </div>
            <div class="col div-button-post">
                <button class="btn btn-outline btn-outline-primary button-post align-middle" id="save-post-card-<?php if (isset($templateParams["id"])) {
                                                                                                                    echo $templateParams["id"];
                                                                                                                } ?>" <?php if (isset($templateParams["disableSave"]) && $templateParams["disableSave"] == true) {
                                                                                                                            echo "disabled";
                                                                                                                        } ?>>
                </button>

            </div>
        </div>
        <div class="comments text-left m-1">
            <?php
            if (isset($templateParams["descrizione"]) && isset($templateParams["username"])) {
                //Descrizione
                echo html_entity_decode('<p><a href="' . getUserProfileHref($templateParams["username"]) . '">' . $templateParams["username"] . '</a>' . ': ' . $templateParams["descrizione"] . '</p>');
            }
            if (isset($templateParams["animals"]) && count($templateParams["animals"]) > 0) {
                $row = '<p>';
                if (count($templateParams["animals"]) == 1) {
                    $row = $row . ' Animale: ';
                } else {
                    $row = $row . ' Animali: ';
                }
                for ($i = 0; $i < count($templateParams["animals"]); $i++) {
                    $single = $templateParams["animals"][$i];
                    $row = $row . '<a href="' . getAnimalProfileHref($single) . '">' . $single . '</a>';
                    if ($i + 1 == count($templateParams["animals"])) {
                        //Non è l'ultimo elemento
                        $row = $row . '.';
                    } else {
                        //Ultimo elemento
                        $row = $row . ', ';
                    }
                }
                $row = $row . '</p>';
                echo html_entity_decode($row);
            }
            if (isset($templateParams["timestamp"]) && isset($templateParams["username"])) {
                echo html_entity_decode('<p>' . 'Post creato alle ' . $templateParams["timestamp"] . '</p>');
            } ?>
            <?php
            if (isset($templateParams["comments"]) && count($templateParams["comments"]) > 0 && isset($templateParams["id"])) {
                $id = $templateParams["id"];
                foreach ($templateParams["comments"] as $comment) {
                    echo html_entity_decode('<p><a href="' . getUserProfileHref($comment["username"]) . '">' . $comment["username"] . '</a>' . ': ' . $comment["testo"] . '</p>');
                    echo html_entity_decode('<button id="' . $id . '-comment-' . $comment["id_commento"] . '">Rispondi</button>');
                    if (isset($templateParams["son-comments-" . $comment["id_commento"]]) && $templateParams["son-comments-" . $comment["id_commento"]] == true) {
                        //Ci sono commenti di risposta
                        if (isset($templateParams["canLoadMoreComments"]) && $templateParams["canLoadMoreComments"] == false) {
                            //Non posso caricare in questa pagina altri commenti, redirect a pagina post singolo
                            echo html_entity_decode('<button id="' . $id . '-son-comment-' . $comment["id_commento"] . '" onclick="window.location.href=\'view-post-profile.php?id=' . $id . '\';" >Leggi le risposte</button>');
                        } else {
                            echo html_entity_decode('<button id="' . $id . '-son-comment-' . $comment["id_commento"] . '">Leggi le risposte</button>');
                        }
                    }
                }
            }
            if (isset($templateParams["more-comments"]) && $templateParams["more-comments"] == true && isset($templateParams["id"])) {
                $id = $templateParams["id"];
                if (isset($templateParams["canLoadMoreComments"]) && $templateParams["canLoadMoreComments"] == false) {
                    //Non posso caricare in questa pagina altri commenti, redirect a pagina post singolo
                    echo html_entity_decode('<button id="' . $id . '-comment-load" onclick="window.location.href=\'view-post-profile.php?id=' . $id . '\';">Carica altri commenti</button>');
                } else {
                    echo html_entity_decode('<button id="' . $id . '-comment-load">Carica altri commenti</button>');
                }
            }
            ?>
            <!--New comment-->
            <div class="row g-0">
                <label for="<?php if (isset($templateParams["id"])) {
                                echo $templateParams["id"];
                            } ?>-commentTextArea" id="<?php if (isset($templateParams["id"])) {
                                                                                    echo $templateParams["id"];
                                                                                } ?>-label"> Aggiungi un commento a questo post:</label>
                <textarea class="rounded col form-control" placeholder="Massimo 200 caratteri" maxlength="200" id="<?php if (isset($templateParams["id"])) {
                                                                                                                        echo $templateParams["id"];
                                                                                                                    } ?>-commentTextArea" name="new-comment"></textarea>
                <button class="rounded col-2" id="<?php if (isset($templateParams["id"])) {
                                                        echo $templateParams["id"];
                                                    } ?>-new-comment">Commenta</button>
            </div>
        </div>
    </div>
</div>
<script src="js/post.js" type="text/javascript"></script>