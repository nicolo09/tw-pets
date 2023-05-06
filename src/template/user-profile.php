<div class="container-fluid text-center pb-2">
    <div class="row">
        <img src="<?php if (isset($templateParams["img"])) {
                    echo $templateParams["img"];
                } ?>" alt="Foto profilo di <?php if (isset($templateParams["username"])) {
                    echo $templateParams["username"];
                } ?>" class="proPic col p-0 mt-2 d-flex justify-content-end" />
        <div class="col p-0 w-25">
            <h1><?php if (isset($templateParams["username"])) {
                    echo $templateParams["username"];
                } ?></h1>
            <h2>Persona/<?php if (isset($templateParams["role"])) {
                    echo $templateParams["role"];
                } ?></h2>
            <p><?php if (isset($templateParams["description"])) {
                    echo $templateParams["description"];
                } ?></p>
        </div>
        <!--Bottoni-->
        <div class="text-center row w-100 g-0">
            <button class="btn btn-outline-primary col m-2"><img src="img/add-user.svg" alt="" class="w-50">Segui</button>
            <button class="btn btn-outline-primary col m-2"><img src="img/pets.svg" alt="" class="w-50">Animali</button>
            <button class="btn btn-outline-primary col m-2"><img src="img/groups.svg" alt="" class="w-50">Followers</button>
        </div>
    </div>

</div>

<!--Galleria immagini-->
<div class="container-fluid g-0 w-100 border-top border-black mt-3 pt-2">
    <?php
    if(isset($templateParams["postimg"])&&isset($templateParams["alt"])){
        if(count($templateParams["postimg"])==isset($templateParams["alt"])&&count($templateParams["postimg"])>0){
            //Ogni immagine deve avere un alt
            $n=count($templateParams["postimg"]);//Hai n elementi, tra 0 e n-1
            $rows=ceil($n/2); //Il numero di righe, almeno una esiste
            $start='<div class="row w-100 g-0">';
            $end='</div>';
            $counter=0;
            for($i=0; $i<$rows; $i++){
                echo html_entity_decode($start);
                $tmp='<img src="'.$templateParams["postimg"][$counter].'" alt="'.$templateParams["alt"][$counter].'" class="col w-50 p-0 border border-black" id="immagine"/>';
                echo html_entity_decode($tmp);
                $counter++;
                if($counter<$n){
                    //Ci sono altre immagini da mostrare
                    $tmp='<img src="'.$templateParams["postimg"][$counter].'" alt="'.$templateParams["alt"][$counter].'" class="col w-50 p-0 border border-black" id="immagine"/>';
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
<script src="js/user-profile.js" type="text/javascript"></script>
