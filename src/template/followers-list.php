<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-8 mx-auto">
            <div class="card-body text-center">
                <h2 class="fw-bold">Followers di <?php echo $templateParams["user"] ?></h2>
            </div>
            <?php if($templateParams["type"] == "animal"): ?>
                <div class="results-container">
                    <h3 class="results-title">P A D R O N I</h3>
                    <?php foreach($owners as $user){
                        $username = $user["username"];
                        $img = $user["immagine"];
                        $href = getUserProfileHref($username);
                        require("result-bar.php");
                    } ?> 
                </div>
            <?php endif; ?>
            <div class="results-container" id="container">
                <?php if($templateParams["type"] == "animal"): ?>
                    <h3 class="results-title">U T E N T I</h3>
                <?php endif; ?>
                <?php if(count($results)): ?>
                    <?php foreach($results as $user){
                        $username = $user["username"];
                        $img = $user["immagine"];
                        $href = getUserProfileHref($username);
                        require("result-bar.php");
                    } ?> 
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
