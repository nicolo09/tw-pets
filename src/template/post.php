<main class="d-flex align-items-center justify-content-center">
    <div class="border border-black rounded m-3 p-0 container">
        <!--Rettangolo-->
        <div class="row w-100 border-bottom border-black b-0 m-0 ">
            <!--Utente che ha postato-->
            <img class="icon rounded-circle border border-black p-0" src=<?php if (isset($templateParams["immagineprofilo"])) {
                                                            echo $templateParams["immagineprofilo"];
                                                        } else {
                                                            echo IMG_DIR."default.jpg";
                                                        } ?> alt=<?php if (isset($templateParams["username"])) {
                                                            echo "Foto profilo di ".$templateParams["username"];
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
        <img class="w-100 border-bottom border-black" src=<?php if (isset($templateParams["immagine"])) {
                                                            echo $templateParams["immagine"];
                                                        } else {
                                                            echo "#";
                                                        } ?> alt=<?php if (isset($templateParams["alt"])) {
                                                            echo $templateParams["alt"];
                                                        } else {
                                                            echo "Alt non presente";
                                                        } ?>>
        <div class="w-100 m-0 border-bottom border-black d-flex justify-content-center">
            <!--Tasti-->
            <div class="border-end border-black row w-50">
                <button class="btn btn-outline col"><img class="w-25" src="img/thumb_up.svg" alt="">136 Mi Piace</button>
            </div>
            <div class="row w-50">
                <button class="btn btn-outline col"><img class="w-25" src="img/star.svg" alt="">Salva</button>
            </div>
        </div>
        <div>
            <h1><?php if (isset($_GET["id"])) {
                    echo $_GET["id"];
                } ?></h1>
        </div>
    </div>
</main>