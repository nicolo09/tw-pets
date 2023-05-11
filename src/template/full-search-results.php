<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-8 mx-auto">
            <div class="results-container" id="container">
                <h2 class="text-center fw-bold mb-3"><?php echo $templateParams["type"] == "animal" ? "Animali" : "Persone" ?> risultanti per "<?php echo $templateParams["search"]?>": </h2>
                <?php for($x = 0; $x < count($templateParams["results"]); $x++): ?> 
                    <a href="view-user-profile.php?username=<?php echo $templateParams["results"][$x]["username"]?>&type=<?php echo $templateParams["type"]?>">
                        <div class="card result-bar"> 
                            <div class="card-body p-2">
                                <div class="result-element"> <!-- img and label on same line to avoid empty space -->
                                    <img class="miniature" src="<?php echo IMG_DIR.$templateParams["results"][$x]["immagine"] ?>" alt="Immagine profilo di <?php echo $templateParams["results"][$x]["username"] ?>"/><span class="fs-4 fw-bold miniatureLabel"><?php echo $templateParams["results"][$x]["username"] ?></span>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endfor; ?>
            </div>
            <div class="d-flex justify-content-center align-items-center mt-4" id="spinner">
                <div class="spinner-border text-primary spinner-border-sm"
                    role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="js/search-results.js"></script>
