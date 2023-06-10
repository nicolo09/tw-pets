<form id="search-form" action="search.php" method="get">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10 col-xl-8 mx-auto">
            <label for="inputSearch" class="text-muted ps-5 pe-5">Cerca persone e animali...</label>
                <div class="card search-bar">
                    <div class="card-body p-2">
                        <div class="input-group">
                            <input type="text" class="form-control bg-transparent" <?php if(isset($templateParams["search"])) { echo " value=".$templateParams["search"] ;} ?> placeholder="Cerca persone e animali..." name="username" id="inputSearch" />
                            <div id="search-button" class="search-button">
                                <img src="img/search.svg" alt="Search icon"/>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if(isset($templateParams["persons"])): ?>
                    <h3 class="results-title">P E R S O N E</h3>
                    <?php if(!empty($templateParams["persons"])): ?>
                        <div class="results-container">
                            <?php for($x = 0; $x < 3 && $x < count($templateParams["persons"]); $x++){
                                $username = $templateParams["persons"][$x]["username"];
                                $img = $templateParams["persons"][$x]["immagine"];
                                $href = getUserProfileHref($username);
                                require("result-bar.php");
                            } ?> 
                        </div>
                        <?php if(count($templateParams["persons"]) > 3): ?>
                            <div class="d-flex mb-2 justify-content-center fs-5"><a href="search-results.php?persons=<?php echo $templateParams["search"] ?>">Mostra tutti</a></div>
                        <?php endif; ?>
                    <?php else: ?>
                        <label class="w-100 text-center text-muted text-decoration-underline my-3">Non ci sono utenti che corrispondo alla ricerca</label>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if(isset($templateParams["animals"])): ?>
                    <h3 class="results-title">A N I M A L I</h3>
                    <?php if(!empty($templateParams["animals"])): ?>
                        <div class="results-container">
                            <?php for($x = 0; $x < 3 && $x < count($templateParams["animals"]); $x++){
                                $username = $templateParams["animals"][$x]["username"];
                                $img = $templateParams["animals"][$x]["immagine"];
                                $href = getAnimalProfileHref($username);
                                require("result-bar.php");
                            } ?> 
                        </div>
                        <?php if(count($templateParams["animals"]) > 3): ?>
                            <div class="d-flex justify-content-center mb-4 fs-5"><a href="search-results.php?animals=<?php echo $templateParams["search"] ?>">Mostra tutti</a></div>
                        <?php endif; ?>
                    <?php else: ?>
                        <label class="w-100 text-center text-muted text-decoration-underline my-3">Non ci sono animali che corrispondo alla ricerca</label>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</form>
<script src="js/search.js"></script>
