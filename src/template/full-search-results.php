<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-8 mx-auto">
            <div class="results-container" id="container">
                <h2 class="text-center fw-bold mb-3"><?php echo $templateParams["type"] == "animal" ? "Animali" : "Persone" ?> risultanti per "<?php echo $templateParams["search"]?>": </h2>
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
