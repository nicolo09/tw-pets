<div class="card mx-auto col-12 col-lg-10 col-xl-8 border-0">
<div class="container-fluid pb-2">
    <div class="row">
        <img src="<?php echo $templateParams["img"];?>" alt="Foto profilo di <?php echo $templateParams["username"];?>" class="proPic col p-0 m-4 d-flex justify-content-end" />
        <div class="col p-0 w-25">
            <h1><?php echo $templateParams["username"];?></h1>
            <h2><?php echo $templateParams["role"];?></h2>
            <p><?php echo $templateParams["description"];?></p>
        </div>
        <!--Bottoni-->
        <div class="text-center row g-0">
            <?php if($templateParams["modifyEnabled"] == true): ?>
                <button class="btn btn-outline-primary col m-2" id="modify">
                    <img src="img/edit-profile.svg" alt="edit-icon" class="w-25"/>Modifica
                </button>
            <?php else: ?>
                <button class="btn btn-outline-primary col m-2" id="follow">
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
            <button class="btn btn-outline-primary col m-2" id="animals" <?php if($templateParams["animalsDisabled"] == true){
                echo "disabled";
            }  
            ?>>
            <img src="img/pets.svg" alt="" class="w-25">Animali</button>
            <?php endif ;?>
            <button class="btn btn-outline-primary col m-2" id="followers">
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
<div class="container-fluid g-0 border-top border-black mt-3 pt-2">
    <?php
    if(isset($templateParams["postimg"])&&isset($templateParams["alt"])&&isset($templateParams["id"])){
        if(count($templateParams["postimg"])==isset($templateParams["alt"])&&count($templateParams["postimg"])>0&&count($templateParams["id"])==count($templateParams["postimg"])){
            //Ogni immagine deve avere un alt
            $n=count($templateParams["postimg"]);//Hai n elementi, tra 0 e n-1
            $rows=ceil($n/2); //Il numero di righe, almeno una esiste
            $start='<div class="row w-100 g-0">';
            $end='</div>';
            $counter=0;
            for($i=0; $i<$rows; $i++){
                echo html_entity_decode($start);
                $tmp='<a href="post.php?id='.$templateParams["id"][$counter].'" class="col w-50 p-0 border border-black"> <img src="'.$templateParams["postimg"][$counter].'" alt="'.$templateParams["alt"][$counter].'" class="w-100"/> </a>';
                echo html_entity_decode($tmp);
                $counter++;
                if($counter<$n){
                    //Ci sono altre immagini da mostrare
                    $tmp='<a href="post.php?id='.$templateParams["id"][$counter].'" class="col w-50 p-0 border border-black"> <img src="'.$templateParams["postimg"][$counter].'" alt="'.$templateParams["alt"][$counter].'" class="w-100" /> </a>';
                    echo html_entity_decode($tmp);
                    $counter++;
                }else{
                    $tmp='<img src="#" alt="" class="col w-50 p-0 border border-black"/>';
                    echo html_entity_decode($tmp);
                    $counter++;
                }
                echo html_entity_decode($end);
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
