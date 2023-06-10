<div class="container-fluid">
    <h1 class="text-center mb-3">Animali di <?php echo $templateParams["user"] ?></h1>
    <div class="mobile-view">
        <?php for ($x = 0; $x < count($templateParams["animals"]); $x += 2) : ?>
            <div class="row pro-pic-container">
                <?php for ($y = 0; $y < 2; $y++) : ?>
                    <div class="col p-0 w-50 text-center">
                        <?php if (isset($templateParams["animals"][$x + $y])) : ?>
                            <div><img class="pro-pic-btn" id="<?php echo $templateParams["animals"][$x + $y]["username"] ?>_image_mobile" title="Vai al profilo di <?php echo $templateParams["animals"][$x + $y]["username"] ?>" src="<?php echo IMG_DIR . $templateParams["animals"][$x + $y]["immagine"] ?>" alt="Profilo di <?php echo $templateParams["animals"][$x + $y]["username"] ?>"></div>
                            <label class="fs-4 fw-bolder pro-pic-label"><?php echo $templateParams["animals"][$x + $y]["username"] ?></label>
                            <?php if (getUserName($dbh) == $templateParams["user"]) : ?>
                                <div class="w-100"><button class="btn manage-button" id="modify_<?php echo $templateParams["animals"][$x + $y]["username"] ?>_mobile">Modifica</button></div>
                            <?php endif ?>
                        <?php endif ?>
                    </div>
                <?php endfor; ?>
            </div>
        <?php endfor; ?>
    </div>
    <div class="desktop-view">
        <?php for ($x = 0; $x < count($templateParams["animals"]); $x += 4) : ?>
            <div class="row pro-pic-container px-5">
                <?php for ($y = 0; $y < 4; $y++) : ?>
                    <div class="col w-25 text-center">
                        <?php if (isset($templateParams["animals"][$x + $y])) : ?>
                            <div class="w-100"><img class="pro-pic-btn" id="<?php echo $templateParams["animals"][$x + $y]["username"] ?>_image_desktop" title="Vai al profilo di <?php echo $templateParams["animals"][$x + $y]["username"] ?>" src="<?php echo IMG_DIR . $templateParams["animals"][$x + $y]["immagine"] ?>" alt="Profilo di <?php echo $templateParams["animals"][$x + $y]["username"] ?>"></div>
                            <label class=" fs-4 fw-bolder pro-pic-label"><?php echo $templateParams["animals"][$x + $y]["username"] ?></label>
                            <?php if (getUserName($dbh) == $templateParams["user"]) : ?>
                                <div class="w-100"><button class="btn manage-button" id="modify_<?php echo $templateParams["animals"][$x + $y]["username"] ?>_desktop">Modifica</button></div>
                            <?php endif ?>
                        <?php endif ?>
                    </div>
                <?php endfor; ?>
            </div>
        <?php endfor; ?>
    </div>
    <?php if (getUserName($dbh) == $templateParams["user"]) : ?>
        <div class="col-12 col-lg-10 my-4 mx-auto">
            <button class="btn btn-primary w-100" id="add-animal-button"><img src="<?php echo IMG_DIR . "add_animal.svg" ?>" alt="">Aggiungi animale</button>
        </div>
    <?php endif ?>
</div>
<script src="/js/animal-explorer.js"></script>
