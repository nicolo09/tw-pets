<h1 class="text-center">Animali di <?php echo $templateParams["user"]?></h1>
<div class="d-grid gap-3">
    <?php for($x = 0; $x < count($templateParams["animals"]); $x+=2): ?>
        <div class="row">
            <?php for($y = 0; $y < 2 && ($x + $y) < count($templateParams["animals"]) ; $y++):?>
                <div>
                    <img src="<?php echo $templateParams["animals"][$x + $y]["immagine"]?>" alt="Profilo di <?php echo $templateParams["animals"][$x + $y]["username"]?>">
                    <label class="text-center"><?php echo $templateParams["animals"][$x + $y]["username"]?></label>
                </div>
            <?php endfor;?>
        </div>        
    <?php endfor; ?>
    <?php if($_SESSION["username"] == $templateParams["user"]):?>
    <a href="add_animal.php">
        <button class="btn btn-outline-primary"><img src="/img/add_animal.svg">Aggiungi animale</button>
    </a>
    <?php endif ?>
</div>