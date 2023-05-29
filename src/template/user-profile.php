<div class="card mx-auto col-12 col-lg-10 col-xl-8 border-0">
<div class="container-fluid pb-2">
    <div class="row">
        <img src="<?php echo $templateParams["img"];?>" alt="Foto profilo di <?php echo $templateParams["username"];?>" class="pro-pic col p-0 m-4 d-flex justify-content-end" />
        <div class="col p-0 w-25 d-flex align-items-top flex-column">
            <h1 class="fw-normal"><?php echo $templateParams["username"];?></h1>
            <h2 class="flex-fill fs-5"><?php echo $templateParams["role"];?></h2>
            <p class="flex-fill"><?php echo $templateParams["description"];?></p>
        </div>
        <!--Bottoni-->
        <div class="text-center row g-0">
            <?php if($templateParams["modifyEnabled"] == true): ?>
                <button class="btn btn-outline-primary col profile-button" id="modify">
                    <img src="img/edit-profile.svg" alt="edit-icon" class="w-25"/>Modifica
                </button>
            <?php else: ?>
                <button class="btn btn-outline-primary col profile-button" id="follow">
                <?php if($templateParams["userFollows"] == true){
                //Utente segue
                    echo html_entity_decode('<img src="img/remove-user.svg" alt="" class="w-25" />Smetti di seguire');
                } else {
                    echo html_entity_decode('<img src="img/add-user.svg" alt="" class="w-25" />Segui');
                }
                ?>
                </button>
            <?php endif; ?>
            <?php if(isset($templateParams["animalAccount"]) && $templateParams["animalAccount"] == true) :?>
            <!--E' un animale, niente bottone animale-->
            <?php else : ?>
            <!--Non è un animale-->
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

<!--Galleria immagini-->
<div class="container-fluid g-0 border-top border-black mt-3">
    <?php
    if(isset($templateParams["postimg"])&&isset($templateParams["alt"])&&isset($templateParams["id"])){
        if(count($templateParams["postimg"])==count($templateParams["alt"])&&count($templateParams["postimg"])>0&&count($templateParams["id"])==count($templateParams["postimg"])){
            //Ogni immagine deve avere un alt
            $n=count($templateParams["postimg"]);//Hai n elementi, tra 0 e n-1
            for($i = 0; $i < $n; $i += 3){
                echo html_entity_decode('<div class="row g-0">');
                for($j = 0; $j < 3 && $j + $i < $n; $j++){
                    echo html_entity_decode('<a href="view-post-profile.php?id='.$templateParams["id"][$i + $j].'" class="col post-preview-container">
                    <img src="'.$templateParams["postimg"][$i + $j].'" alt="'.$templateParams["alt"][$i + $j].'" class="post-preview"/></a>');
                }
                echo html_entity_decode('</div>');
            }
        }else{
            //Se hai un numero immagini diverso dal numero di alt-> non mostro nessuna immagine
            echo html_entity_decode('<h3 class="text-center">Nessun post</h3>');
        }
    }else{
        //Se non hai settato le immagini
        echo html_entity_decode('<h3 class="text-center">Nessun post</h3>');
    }
    
    ?>
</div>
</div>
<script src="js/user-profile.js" type="text/javascript"></script>
