<h1 class="text-center mb-3">Post salvati</h1>
<div id="post-list">
<?php
$posts = $dbh->getSavedPosts($_SESSION["username"], 10, 0);

require("post-list.php");
?>
</div>
<div class="d-flex justify-content-center align-items-center mt-4">
    <div class="spinner-border text-primary spinner-border-sm" id="posts-spinner" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>
<script src="js/post-utils.js"></script>
<script src="js/favorite-posts.js"></script>