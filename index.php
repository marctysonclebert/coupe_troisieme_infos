<?php
require_once('./classes/database/Database.class.php');
require_once('./classes/equipes/TeamModel.class.php');
require_once('./classes/equipes/TeamView.class.php');

$imagesLink = './css/images/';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil | Coupe 3ème Infos</title>
    <link type="text/css" rel="stylesheet" href="./css/accueil.css">
</head>

<body>
    <section id="accueil">
        <h1>Coupe 3ème Infos</h1>
        <img src="./css/images/world-cup.png" alt="World Cup Trophy">
        <a href="./includes/championship.php">Voir Le Calendrier</a>
    </section>

    <section>
        <h1>Toutes Les Equipes Du Championnat</h1>

        <div id="equipes">

            <?php
            $teamsView = new TeamView();
            foreach ($teamsView->teamsList() as $equipe) {
            ?>
                <div class="equipe">
                    <img src=<?= $imagesLink . $equipe->drapeau ?> alt=<?= $equipe->nom ?> class="equipe-img">
                    <div class="equipe-infos">
                        <p class="nom"><span>Nom: </span> <?= $equipe->nom ?></p>
                        <p class="surnom"><span>Surnom: </span> <?= $equipe->surnom ?></p>
                        <p class="nombre-fois-champions">
                            <span>Nombre de titre: </span> <?= $equipe->titre ?>&star;
                        </p>
                        <p class="description">
                            <span>Description: </span> <?= $equipe->descr ?>
                        </p>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>
    </section>

    <section>
        <h1>Coupe Du Monde 2021</h1>

        <div class="images">
            <img src="./css/images/ball-1.jpg" alt="Ball 1">
            <img src="./css/images/fans.jpg" alt="Fans">
            <img src="./css/images/ball.jpg" alt="Ball">
            <img src="./css/images/field.jpg" alt="Field">
            <img src="./css/images/ball-2.jpg" alt="Ball 2">
        </div>
    </section>

    <footer>
        Copyright &copy; 2021-2022. All Right Reserved
    </footer>
</body>

</html>