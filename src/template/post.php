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
                                                            } ?> alt=<?php if (isset($templateParams["username"])) {
                                                                            echo "Foto profilo di " . $templateParams["username"];
                                                                        } else {
                                                                            echo "Foto profilo di utente non esistente";
                                                                        } ?>>
            <a class="align-items-center" <?php if (isset($templateParams["username"])) {
                                                echo html_entity_decode('href=view-user-profile.php?username=' . $templateParams["username"] . '&type=person');
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
                            } ?> alt=<?php if (isset($templateParams["alt"])) {
                                            echo $templateParams["alt"];
                                        } else {
                                            echo "Alt non presente";
                                        } ?>>
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
        <div class="comments text-left">
            <?php
            if (isset($templateParams["descrizione"]) && isset($templateParams["username"])) {
                //Descrizione
                echo html_entity_decode("<p>" . $templateParams["username"] . ": " . $templateParams["descrizione"] . "</p>");
            }
            //TODO:Aggiungi link ad animali 
            if (isset($templateParams["animals"]) && count($templateParams["animals"]) > 0) {
                $row = "<p>";
                if (count($templateParams["animals"]) == 1) {
                    $row = $row . " Animale: ";
                } else {
                    $row = $row . " Animali: ";
                }
                for ($i=0; $i<count($templateParams["animals"]); $i++ ) {
                    $single=$templateParams["animals"][$i];
                    $row = $row . $single;
                    if($i+1==count($templateParams["animals"])){
                        //Non è l'ultimo elemento
                        $row=$row.".";
                    }else{
                        //Ultimo elemento
                        $row=$row.", ";
                    }
                }
                $row = $row . "</p>";
                echo html_entity_decode($row);
            }
            if (isset($templateParams["timestamp"]) && isset($templateParams["username"])) {
                echo html_entity_decode("<p>" . "Post creato alle " . $templateParams["timestamp"] . "</p>");
            } ?>
        </div>
    </div>
</div>
<script src="js/post.js" type="text/javascript"></script>