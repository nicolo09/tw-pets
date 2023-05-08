<h1 class="text-center">Animali di <?php echo $templateParams["user"]?></h1>
<?php if(isset($templateParams["error"])):?>
<p class="text-danger text-center text-decoration-underline"><?php echo $templateParams["error"]; ?></p>
<?php endif; ?>
<div class="d-grid gap-3">
    <div class="mobile-view">
    <?php for($x = 0; $x < count($templateParams["animals"]); $x+=2): ?>
        <div class="row proPicContainer">
            <?php for($y = 0; $y < 2; $y++):?>
                <div class="col w-50 text-center">
                    <?php if(isset($templateParams["animals"][$x + $y])): ?>
                    <div class="w-100"><img class="proPicBtn" 
                        src="<?php echo IMG_DIR.$templateParams["animals"][$x + $y]["immagine"]?>" 
                        alt="Profilo di <?php echo $templateParams["animals"][$x + $y]["username"]?>"
                        onclick='goToAnimal("<?php echo $templateParams["animals"][$x + $y]["username"]?>")'></div>
                        <label class="fs-4 fw-bolder proPicLabel"><?php echo $templateParams["animals"][$x + $y]["username"]?></label>
                        <?php if(getUserName($dbh) == $templateParams["user"]):?> 
                            <div class="w-100"><button class="btn manage-button" id="modify_<?php echo $templateParams["animals"][$x + $y]["username"]?>_mobile">Modifica</button></div>
                        <?php endif ?>
                    <?php endif ?>
                </div>
            <?php endfor;?>
        </div>
    <?php endfor; ?>
    </div>
    <div class="desktop-view">
    <?php for($x = 0; $x < count($templateParams["animals"]); $x+=4): ?>
        <div class="row proPicContainer">
            <?php for($y = 0; $y < 4; $y++):?>
                <div class="col w-25 text-center">
                    <?php if(isset($templateParams["animals"][$x + $y])): ?>
                        <div class="w-100"><img class="proPicBtn" 
                        src="<?php echo IMG_DIR.$templateParams["animals"][$x + $y]["immagine"]?>" 
                        alt="Profilo di <?php echo $templateParams["animals"][$x + $y]["username"]?>"
                        onclick='goToAnimal("<?php echo $templateParams["animals"][$x + $y]["username"]?>")'></div>
                        <label class=" fs-4 fw-bolder proPicLabel"><?php echo $templateParams["animals"][$x + $y]["username"]?></label>
                        <?php if(getUserName($dbh) == $templateParams["user"]):?>
                            <div class="w-100"><button class="btn manage-button" id="modify_<?php echo $templateParams["animals"][$x + $y]["username"]?>_desktop">Modifica</button></div>
                        <?php endif ?>
                    <?php endif ?>
                </div>
            <?php endfor;?>
        </div>
    <?php endfor; ?>
    </div>        
    <?php if(getUserName($dbh) == $templateParams["user"]):?>
    <button class="btn btn-primary" id="add-animal-button"><img src="/img/add_animal.svg" alt="add animal icon">Aggiungi animale</button>
    <?php endif ?>
</div>
<script src="/js/animal-explorer-utils.js"></script>