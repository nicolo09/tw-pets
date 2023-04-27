<h1 class="text-center">Animali di <?php echo $templateParams["user"]?></h1>
<div class="d-grid gap-3">
    <?php for($x = 0; $x < count($templateParams["animals"]); $x+=2): ?>
        <div class="row proPicContainer">
            <?php for($y = 0; $y < 2 && ($x + $y) < count($templateParams["animals"]) ; $y++):?>
                <div class="col text-center">
                    <img class="proPicBtn" 
                        src="<?php echo IMG_DIR.$templateParams["animals"][$x + $y]["immagine"]?>" 
                        alt="Profilo di <?php echo $templateParams["animals"][$x + $y]["username"]?>"
                        onclick='goToAnimal("<?php echo $templateParams["animals"][$x + $y]["username"]?>")'>
                    <label class="w-100 fs-4 fw-bolder"><?php echo $templateParams["animals"][$x + $y]["username"]?></label>
                </div>
            <?php endfor;?>
        </div>        
    <?php endfor; ?>
    <?php if($_SESSION["username"] == $templateParams["user"]):?>
    <button class="btn btn-primary" id="add-animal-button"><img src="/img/add_animal.svg">Aggiungi animale</button>
    <?php endif ?>
</div>
<script src="/js/animal-explorer-utils.js" type="text/javascript"></script>