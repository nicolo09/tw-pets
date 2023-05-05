<form id="search-form" action="search.php" method="get">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10 col-xl-8 mx-auto">
                <div class="card search-bar">
                    <div class="card-body p-2">
                        <div class="input-group input-group-lg">
                            <input type="text" class="form-control form-control-lg bg-transparent" placeholder="Cerca persone e animali.." aria-label="Type Keywords" aria-describedby="basic-addon2" name="username"/>
                            <div id="search-button" class="search-button">
                                <img src="img/search.svg" />
                            </div>
                        </div>
                    </div>
                </div>
                <?php if(isset($templateParams["persons"])): ?>
                    <h3 class="results-title">P E R S O N E</h3>
                    <?php if(!empty($templateParams["persons"])): ?>
                        <div class="results-container">
                            <?php for($x = 0; $x < 3 && $x < count($templateParams["persons"]); $x++): ?> <!-- TODO change to normal for cicle (try with 5 iter) -->
                                <div class="card result-bar">
                                    <div class="card-body p-2">
                                        <div class="result-element">
                                            <img class="miniature" src="<?php echo IMG_DIR.$templateParams["persons"][$x]["immagine"] ?>" alt="Immagine profilo di <?php echo $templateParams["persons"][$x]["username"] ?>"/>
                                            <label class="fs-4 fw-bold"><?php echo $templateParams["persons"][$x]["username"] ?></label>
                                        </div>
                                    </div>
                                </div>
                            <?php endfor; ?>
                        </div>
                    <?php else: ?>
                        <label class="w-100 text-center text-muted text-decoration-underline my-3">Non ci sono utenti che corrispondo alla ricerca</label>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if(isset($templateParams["animals"])): ?>
                    <h3 class="results-title">A N I M A L I</h3>
                    <?php if(!empty($templateParams["animals"])): ?>
                        <div class="results-container">
                            <?php for($x = 0; $x < 3 && $x < count($templateParams["animals"]); $x++): ?> <!-- TODO change to normal for cicle (try with 5 iter) -->
                                <div class="card result-bar">
                                    <div class="card-body p-2">
                                        <div class="result-element">
                                            <img class="miniature" src="<?php echo IMG_DIR.$templateParams["animals"][$x]["immagine"] ?>" alt="Immagine profilo di <?php echo $templateParams["animals"][$x]["username"] ?>"/>
                                            <label class="fs-4 fw-bold"><?php echo $templateParams["animals"][$x]["username"] ?></label>
                                        </div>
                                    </div>
                                </div>
                            <?php endfor; ?>
                        </div>
                    <?php else: ?>
                        <label class="w-100 text-center text-muted text-decoration-underline my-3">Non ci sono animali che corrispondo alla ricerca</label>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</form>
<script src="js/search.js"></script>
