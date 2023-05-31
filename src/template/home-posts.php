<?php
$result = $dbh->getPostsForUser(getUserName($dbh), 10, 0);
echo '<div id="post-list">';
require("post-list.php");
echo '</div>'
?>

<div class="d-flex justify-content-center align-items-center mt-4" id="spinner">
    <div class="spinner-border text-primary spinner-border-sm" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<script src="js/post-utils.js"></script>
<script src="js/home.js"></script>
