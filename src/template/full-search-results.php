<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-8 mx-auto">
            <div class="results-container">
                <h2 class="text-center fw-bold mb-3"><?php echo $templateParams["type"]?> risultanti per "<?php echo $templateParams["search"]?>": </h2>
                <?php for($x = 0; $x < count($templateParams["results"]); $x++): ?> 
                    <div class="card result-bar">
                        <div class="card-body p-2">
                            <div class="result-element">
                                <img class="miniature" src="<?php echo IMG_DIR.$templateParams["results"][$x]["immagine"] ?>" alt="Immagine profilo di <?php echo $templateParams["results"][$x]["username"] ?>"/>
                                <label class="fs-4 fw-bold"><?php echo $templateParams["results"][$x]["username"] ?></label>
                            </div>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>
</div>