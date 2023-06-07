<div class="card mx-auto col-12 col-lg-10 col-xl-8 border-0">
<div class="container-fluid pb-2">
    <div class="row">
        <img src="<?php echo $templateParams["img"];?>" alt="Foto profilo di <?php echo $templateParams["username"];?>" class="pro-pic col p-0 m-4 d-flex justify-content-end" />
        <div class="col p-0 w-25 d-flex align-items-top flex-column">
            <h1 class="fw-normal"><?php echo $templateParams["username"];?></h1>
            <h2 class="flex-fill fs-5"><?php echo $templateParams["role"];?></h2>
            <p class="flex-fill"><?php echo $templateParams["description"];?></p>
        </div>
        <!-- Buttons -->
        <div class="text-center row g-0">
            <?php if($templateParams["modifyEnabled"] == true): ?>
                <button class="btn btn-outline-primary col profile-button" id="modify">
                    <img src="img/edit-profile.svg" alt="edit-icon" class="w-25"/>Modifica
                </button>
            <?php else: ?>
                <button class="btn btn-outline-primary col profile-button" id="follow">
                <?php if($templateParams["userFollows"] == true){
                //The user follows this account
                    echo '<img src="img/remove-user.svg" alt="" class="w-25" />Smetti di seguire';
                } else {
                    echo '<img src="img/add-user.svg" alt="" class="w-25" />Segui';
                }
                ?>
                </button>
            <?php endif; ?>
            <?php if(isset($templateParams["animalAccount"]) && $templateParams["animalAccount"] == true) :?>
            <!-- It's an animal account, there is no need for an animals button -->
            <?php else : ?>
            <!-- It's a person account -->
            <button class="btn btn-outline-primary col profile-button" id="animals" <?php if($templateParams["animalsDisabled"] == true){
                echo "disabled";
            }  
            ?>>
            <img src="img/pets.svg" alt="" class="w-25">Animali</button>
            <?php endif ;?>
            <button class="btn btn-outline-primary col profile-button" id="followers">
            <img src="img/groups.svg" alt="" class="w-25">Followers</button>
        </div>
        <?php 
        if(isset($templateParams["success"]) && $templateParams["success"] == 0){
            echo "<p class='text-danger'> C'è stato un errore </p>";
        }
        ?>
    </div>

</div>

<!-- User posts -->
<div class="container-fluid g-0 border-top border-black mt-3">
    <?php
    if(isset($templateParams["postimg"])&&isset($templateParams["alt"])&&isset($templateParams["id"])){
        if(count($templateParams["postimg"])==count($templateParams["alt"])&&count($templateParams["postimg"])>0&&count($templateParams["id"])==count($templateParams["postimg"])){
            //Every image must have an alt
            $n=count($templateParams["postimg"]);//There are n posts
            for($i = 0; $i < $n; $i += 3){
                echo '<div class="row g-0">';
                for($j = 0; $j < 3 && $j + $i < $n; $j++){
                    echo '<a href="view-post-profile.php?id='.$templateParams["id"][$i + $j].'" class="col post-preview-container">
                    <img src="'.$templateParams["postimg"][$i + $j].'" alt="'.$templateParams["alt"][$i + $j].'" class="post-preview"/></a>';
                }
                echo '</div>';
            }
        }else{
            //If the number of posts is different from the number of alt nothing is shown
            echo '<h3 class="text-center">Nessun post</h3>';
        }
    }else{
        //There are no posts
        echo '<h3 class="text-center">Nessun post</h3>';
    }
    
    ?>
</div>
</div>
<script src="js/user-profile.js"></script>
