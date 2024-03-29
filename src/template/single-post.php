<div class="card mx-auto col-12 col-sm-8 col-md-6 col-lg-5 col-xl-4 border-black align-items-center justify-content-center post" id="post-card-<?php echo $templateParams["id"];?>">
    <div class="card-header row border-bottom border-black post-header p-1">
        <!-- Post's maker -->
        <div class="col p-0">
            <a class="align-items-center" href="<?php echo getUserProfileHref($templateParams["username"]);?>" id="<?php echo $templateParams["id"];?>-creator">
                <img class="post-pic text-center img-fluid me-2" src="<?php echo $templateParams["immagineprofilo"];?>" alt="<?php echo "Foto profilo di " . $templateParams["username"];?>"><?php echo $templateParams["username"]; ?>
            </a>
        </div>
        <?php if($templateParams["username"] == getUserName($dbh) && !isset($templateParams["list"]) && $templateParams["id"] != -1):?>
            <div class="col d-flex justify-content-end p-0">
                <button class="btn btn-outline-danger py-0 m-1" id="delete-post-card-<?php echo $templateParams["id"];?>">Elimina</button>
            </div>
        <?php endif ?>
    </div>
    <!-- Image -->
    <img class="w-100 <?php if (isset($templateParams["list"])) echo "home-post"?>" src=<?php echo $templateParams["immagine"];?> alt="<?php echo $templateParams["alt"];?>" id="post-img-<?php echo $templateParams["id"];?>">
    <div class="card-footer w-100 p-0 m-0">
        <div class="w-100 m-0 d-flex justify-content-center row">
            <!-- Buttons -->
            <div class="col div-button-post">
                <button class="btn btn-outline btn-outline-primary button-post align-middle" id="like-post-card-<?php echo $templateParams["id"];?>">
                </button>

            </div>
            <div class="col div-button-post">
                <button class="btn btn-outline btn-outline-primary button-post align-middle" id="save-post-card-<?php echo $templateParams["id"];?>">
                </button>

            </div>
        </div>
        <div class="comments text-left m-1" id="comments-<?php echo $templateParams["id"] ?>">
            <?php
            //Description
            echo '<p><a href="' . getUserProfileHref($templateParams["username"]) . '">' . $templateParams["username"] . '</a>' . ': ' . $templateParams["descrizione"] . '</p>';
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
                        //It's the last animal
                        $row = $row . '.';
                    } else {
                        //It's not the last animal
                        $row = $row . ', ';
                    }
                }
                $row = $row . '</p>';
                echo $row;
            }
            echo '<p>' . 'Post creato alle ' . $templateParams["timestamp"] . '</p>';
            ?>
            <div class="comment-slider">
                <div class="comment-container">
                    <?php
                    if (isset($templateParams["comments"]) && count($templateParams["comments"]) > 0) {
                        $id = $templateParams["id"];
                        foreach ($templateParams["comments"] as $comment) {
                            echo '<p><a href="' . getUserProfileHref($comment["username"]) . '">' . $comment["username"] . '</a>' . ': ' . $comment["testo"] . '</p>';
                            echo '<p class="text-muted">' . date("d/m/Y H:i", strtotime($comment["timestamp"])) . '</p>';
                            echo '<button id="' . $id . '-comment-' . $comment["id_commento"] . '" class="comment-answer rounded btn btn-outline-primary">Rispondi</button>';
                            if (isset($templateParams["son-comments-" . $comment["id_commento"]]) && $templateParams["son-comments-" . $comment["id_commento"]] == true) {
                                //The comment has answers
                                echo '<button id="' . $id . '-son-comment-' . $comment["id_commento"] . '" class="rounded btn btn-outline-primary">Leggi le risposte</button>';
                            }
                        }
                    }
                    if (isset($templateParams["more-comments"]) && $templateParams["more-comments"] == true) {
                        $id = $templateParams["id"];
                        echo '<a href="view-post-profile.php?id='.$id.'">Leggi i commenti</a>';
                    }
                    ?>
                </div>
                <?php 
                    echo '<div class="d-flex justify-content-center align-items-center mt-4 spinner-post d-none" id="spinner-post-' . $templateParams["id"] . '"> <div class="spinner-border text-primary spinner-border-sm"
                            role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>';
                ?>
            </div>
            <!--New comment-->
            <div class="row g-0">
                <label for="<?php echo $templateParams["id"];?>-commentTextArea" id="<?php echo $templateParams["id"];?>-label"> Aggiungi un commento a questo post:</label>
                <textarea class="rounded col form-control" placeholder="Massimo 200 caratteri" maxlength="200" id="<?php echo $templateParams["id"];?>-commentTextArea" name="new-comment"></textarea>
                <button class="rounded col-3 new-comment btn" id="<?php echo $templateParams["id"];?>-new-comment">Commenta</button>
            </div>
        </div>
    </div>
</div>