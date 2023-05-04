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
    <div class="row w-100 g-0">
        <img src="img/dog_small.jpg" alt="cani sulla spiaggia" class="col w-50 p-0 border border-black"/>
        <img src="img/dog_small.jpg" alt="cani sulla spiaggia" class="col w-50 p-0 border border-black"/>
    </div>
    <div class="row w-100 g-0">
        <img src="img/dog_small.jpg" alt="cani sulla spiaggia" class="col w-50 p-0 border border-black"/>
        <img src="img/dog_small.jpg" alt="cani sulla spiaggia" class="col w-50 p-0 border border-black"/>
    </div>
    <div class="row w-100 g-0">
        <img src="img/dog_small.jpg" alt="cani sulla spiaggia" class="col w-50 p-0 border border-black"/>
        <img src="img/cat.jpg" alt="gatto arancione" class="col w-50 p-0 border border-black"/>
    </div>
    <div class="row w-100 g-0">
        <img src="img/dog_small.jpg" alt="cani sulla spiaggia" class="col w-50 p-0 border border-black"/>
        <img src="#" alt="" class="col w-50 p-0 border border-black"/>
    </div>
</div>