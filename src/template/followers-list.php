<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-8 mx-auto">
            <div class="card-body text-center">
                <h2 class="fw-bold">Followers di <?php echo $templateParams["user"] ?></h2>
            </div>
            <?php if($templateParams["type"] == "animal"): ?>
                <div class="results-container">
                    <h3 class="results-title">P A D R O N I</h3>
                    <?php for($x = 0; $x < count($templateParams["owners"]); $x++): ?> 
                        <a href="<?php echo getUserProfileHref($templateParams["owners"][$x]["username"])?>">
                            <div class="card result-bar"> 
                                <div class="card-body p-2">
                                    <div class="result-element"> <!-- img and label on same line to avoid empty space -->
                                        <img class="miniature" src="<?php echo IMG_DIR.$templateParams["owners"][$x]["immagine"] ?>" alt="Immagine profilo di <?php echo $templateParams["owners"][$x]["username"] ?>"/><span class="fs-4 miniature-text"><?php echo $templateParams["owners"][$x]["username"] ?></span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
            <div class="results-container" id="container">
                <?php if($templateParams["type"] == "animal"): ?>
                    <h3 class="results-title">U T E N T I</h3>
                <?php endif; ?>
                <?php if(count($templateParams["results"])): ?>
                    <?php for($x = 0; $x < count($templateParams["results"]); $x++): ?> 
                        <a href="<?php echo getUserProfileHref($templateParams["results"][$x]["username"])?>">
                            <div class="card result-bar"> 
                                <div class="card-body p-2">
                                    <div class="result-element"> <!-- img and label on same line to avoid empty space -->
                                        <img class="miniature" src="<?php echo IMG_DIR.$templateParams["results"][$x]["immagine"] ?>" alt="Immagine profilo di <?php echo $templateParams["results"][$x]["username"] ?>"/><span class="fs-4 miniature-text"><?php echo $templateParams["results"][$x]["username"] ?></span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    <?php endfor; ?>
                    <div class="d-flex justify-content-center align-items-center mt-4" id="spinner">
                        <div class="spinner-border text-primary spinner-border-sm"
                            role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                <?php else: ?>
                    <label class="w-100 text-center text-muted text-decoration-underline my-3">Questo account non ha follower al momento</label>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<script src="js/followers.js"></script>
