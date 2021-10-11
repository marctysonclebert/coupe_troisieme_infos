<?php
include_once('..\autoload\autoloader-class.php');

$imageLink = '../css/images/';

//Teams
$teamView = new TeamView();
$teamControlleur = new TeamController();

//Games
$gameView = new GameView();
$gameControlleur = new GameController();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link type="text/css" rel="stylesheet" href="../css/championship.css">
    <title>Calendrier | Coupe 3ème Infos</title>
</head>

<body>

    <a href="../index.php">Retour à la page d'accueil</a>

    <table border="1">
        <thead>
            <tr>
                <th>Lot 1</th>
                <th>Lot 2</th>
                <th>Lot 3</th>
                <th>Lot 4</th>
            </tr>
        </thead>
        <tbody>
            <!-- Première Ligne -->
            <tr>
                <?php
                $teamsLigne1 = $teamView->firstLineTeams();
                foreach ($teamsLigne1 as $team) {
                ?>
                    <td>
                        <div>
                            <div>
                                <div>
                                    <?= $team->nom; ?>
                                </div>
                                <div>
                                    <img src=<?= $imageLink . $team->drapeau; ?> alt=<?= $team->nom; ?>>
                                </div>
                            </div>
                        </div>
                    </td>
                <?php
                }
                ?>
            </tr>

            <!-- Deuxième Ligne -->
            <tr>
                <?php
                $teamsLigne2 = $teamView->secondLineTeams();
                foreach ($teamsLigne2 as $team) {
                ?>
                    <td>
                        <div>
                            <div>
                                <div>
                                    <?= $team->nom; ?>
                                </div>
                                <div>
                                    <img src=<?= $imageLink . $team->drapeau; ?> alt=<?= $team->nom; ?>>
                                </div>
                            </div>
                        </div>
                    </td>
                <?php
                }
                ?>
            </tr>
        </tbody>
    </table>

    <?php
    if (isset($_POST['tirage'])) {
        $teamControlleur->draw();
    }

    if (isset($_POST['unDraw'])) {
        $teamControlleur->unDraw();
        $gameControlleur->gamesReset();
    }

    if (isset($_POST['jouer'])) {
        $idMatch = $_POST['idMatch'];
        $scoreEquipe1 = $_POST['scoreEquipe1'];
        $scoreEquipe2 = $_POST['scoreEquipe2'];

        $gameControlleur->playGame($idMatch, $scoreEquipe1, $scoreEquipe2);
    }
    ?>

    <?php
    if ($teamView->canMakeDraw()) {
    ?>
        <!-- Le button du tirage avant le tirage -->
        <form action="./championship.php" method="POST">
            <input type="submit" name="tirage" value="Tirage">
        </form>
    <?php
    } else {
        $gameControlleur->initializeGamesTeams('Premier Tour');
    ?>
        <!-- Le button du tirage après le tirage -->
        <form action="./championship.php" method="POST">
            <input type="submit" class="termine" name="tirage" value="Tirage" disabled>
        </form>

        <!-- Les groupes après le tirage -->
        <table border="1">
            <thead>
                <tr>
                    <th></th>
                    <th>Groupe A</th>
                    <th>Groupe B</th>
                </tr>
            </thead>
            <tbody>
                <?php

                $groupeA = $teamView->getGroupA();
                $groupeB = $teamView->getGroupB();

                foreach ($groupeA as $key => $team) {
                ?>
                    <tr>
                        <td><?= $team->idLot ?><sup>e</sup> Tête De Série (TDS)</td>
                        <td>
                            <div>
                                <div>
                                    <div>
                                        <?= $team->nom; ?>
                                    </div>
                                    <div>
                                        <img src=<?= $imageLink . $team->drapeau; ?> alt=<?= $team->nom; ?>>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>
                                <div>
                                    <div>
                                        <img src=<?= $imageLink . $groupeB[$key]->drapeau; ?> alt=<?= $groupeB[$key]->nom; ?>>
                                    </div>
                                    <div>
                                        <?= $groupeB[$key]->nom; ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>

        <!-- Les matchs du groupe A -->
        <table border="1">
            <thead>
                <tr>
                    <th>Groupe A</th>
                    <th>Affiche</th>
                    <th>Score</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($gameView->listGamesA() as $game) {
                ?>
                    <tr>
                        <td>Match <?= $game->id; ?></td>
                        <td>
                            <div>
                                <div>
                                    <div>
                                        <?= $game->equipe1->nom; ?>
                                    </div>
                                    <div>
                                        <img src=<?= $imageLink . $game->equipe1->drapeau; ?> alt=<?= $game->equipe1->nom; ?>>
                                    </div>
                                </div>
                                <span>VS</span>
                                <div>
                                    <div>
                                        <img src=<?= $imageLink . $game->equipe2->drapeau; ?> alt=<?= $game->equipe2->nom; ?>>
                                    </div>
                                    <div>
                                        <?= $game->equipe2->nom; ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <form action="./championship.php" method="POST">
                                <?php
                                if (!$game->peutJouer && $game->termine) {
                                ?>
                                    <input type="number" readonly value=<?= $game->scoreEquipe1; ?>>
                                    <input type="number" readonly value=<?= $game->scoreEquipe2; ?>>
                                    <input type="submit" class="termine" value="Terminé" disabled>
                                <?php
                                } elseif (!$game->peutJouer) {
                                ?>
                                    <input type="number" readonly value=<?= $game->scoreEquipe1; ?>>
                                    <input type="number" readonly value=<?= $game->scoreEquipe2; ?>>
                                    <input type="submit" class="verouille" value="Vérouillé" disabled>
                                <?php
                                } else {
                                ?>
                                    <input type="hidden" min="0" max="20" name="idMatch" value=<?= $game->id; ?>>
                                    <input type="number" min="0" max="20" name="scoreEquipe1" value=<?= $game->scoreEquipe1; ?>>
                                    <input type="number" min="0" max="20" name="scoreEquipe2" value=<?= $game->scoreEquipe2; ?>>
                                    <input type="submit" min="0" max="20" name="jouer" value="Jouer">
                                <?php
                                }
                                ?>
                            </form>
                        </td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>

        <!-- Les matchs du groupe B -->
        <table border="1">
            <thead>
                <tr>
                    <th>Groupe B</th>
                    <th>Affiche</th>
                    <th>Score</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($gameView->listGamesB() as $game) {
                ?>
                    <tr>
                        <td>Match <?= $game->id; ?></td>
                        <td>
                            <div>
                                <div>
                                    <div>
                                        <?= $game->equipe1->nom; ?>
                                    </div>
                                    <div>
                                        <img src=<?= $imageLink . $game->equipe1->drapeau; ?> alt=<?= $game->equipe1->nom; ?>>
                                    </div>
                                </div>
                                <span>VS</span>
                                <div>
                                    <div>
                                        <img src=<?= $imageLink . $game->equipe2->drapeau; ?> alt=<?= $game->equipe2->nom; ?>>
                                    </div>
                                    <div>
                                        <?= $game->equipe2->nom; ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <form action="./championship.php" method="POST">
                                <?php
                                if (!$game->peutJouer && $game->termine) {
                                ?>
                                    <input type="number" readonly value=<?= $game->scoreEquipe1; ?>>
                                    <input type="number" readonly value=<?= $game->scoreEquipe2; ?>>
                                    <input type="submit" class="termine" value="Terminé" disabled>
                                <?php
                                } elseif (!$game->peutJouer) {
                                ?>
                                    <input type="number" readonly value=<?= $game->scoreEquipe1; ?>>
                                    <input type="number" readonly value=<?= $game->scoreEquipe2; ?>>
                                    <input type="submit" class="verouille" value="Vérouillé" disabled>
                                <?php
                                } else {
                                ?>
                                    <input type="hidden" min="0" max="20" name="idMatch" value=<?= $game->id; ?>>
                                    <input type="number" min="0" max="20" name="scoreEquipe1" value=<?= $game->scoreEquipe1; ?>>
                                    <input type="number" min="0" max="20" name="scoreEquipe2" value=<?= $game->scoreEquipe2; ?>>
                                    <input type="submit" min="0" max="20" name="jouer" value="Jouer">
                                <?php
                                }
                                ?>
                            </form>
                        </td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>

        <?php

        if ($gameView->isPhaseDone('Premier Tour')) {

        ?>
            <!-- Le classement du groupe A -->
            <table border="1">
                <thead>
                    <tr>
                        <th>Groupe A</th>
                        <th>MJ</th>
                        <th>MG</th>
                        <th>MN</th>
                        <th>MP</th>
                        <th>BP</th>
                        <th>BC</th>
                        <th>Diff</th>
                        <th>Points</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $teamControlleur->setTeamQualify();

                    $firstGroupesOrdered = $teamView->getClassementOfGroup(1);

                    foreach ($firstGroupesOrdered as $team) {
                    ?>
                        <tr>
                            <td class=<?= ($team->qualifie) ? 'qualifie' : 'elimine'; ?>>
                                <div>
                                    <div>
                                        <div>
                                            <?= $team->nom; ?>
                                        </div>
                                        <div>
                                            <img src=<?= $imageLink . $team->drapeau; ?> alt=<?= $team->nom; ?>>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class=<?= ($team->qualifie) ? 'qualifie' : 'elimine'; ?>>
                                <?= $team->matchJoue; ?>
                            </td>
                            <td class=<?= ($team->qualifie) ? 'qualifie' : 'elimine'; ?>>
                                <?= $team->matchGagne; ?>
                            </td>
                            <td class=<?= ($team->qualifie) ? 'qualifie' : 'elimine'; ?>>
                                <?= $team->matchNull; ?>
                            </td>
                            <td class=<?= ($team->qualifie) ? 'qualifie' : 'elimine'; ?>>
                                <?= $team->matchPerdu; ?>
                            </td>
                            <td class=<?= ($team->qualifie) ? 'qualifie' : 'elimine'; ?>>
                                <?= $team->butPour; ?>
                            </td>
                            <td class=<?= ($team->qualifie) ? 'qualifie' : 'elimine'; ?>>
                                <?= $team->butContre; ?>
                            </td>
                            <td class=<?= ($team->qualifie) ? 'qualifie' : 'elimine'; ?>>
                                <?= $team->diffBut; ?>
                            </td>
                            <td class=<?= ($team->qualifie) ? 'qualifie' : 'elimine'; ?>>
                                <?= $team->points; ?>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>

            <!-- Le classement du groupe B -->
            <table border="1">
                <thead>
                    <tr>
                        <th>Groupe B</th>
                        <th>MJ</th>
                        <th>MG</th>
                        <th>MN</th>
                        <th>MP</th>
                        <th>BP</th>
                        <th>BC</th>
                        <th>Diff</th>
                        <th>Points</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $secondGroupesOrdered = $teamView->getClassementOfGroup(2);

                    foreach ($secondGroupesOrdered as $team) {
                    ?>
                        <tr>
                            <td class=<?= ($team->qualifie) ? 'qualifie' : 'elimine'; ?>>
                                <div>
                                    <div>
                                        <div>
                                            <?= $team->nom; ?>
                                        </div>
                                        <div>
                                            <img src=<?= $imageLink . $team->drapeau; ?> alt=<?= $team->nom; ?>>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class=<?= ($team->qualifie) ? 'qualifie' : 'elimine'; ?>>
                                <?= $team->matchJoue; ?>
                            </td>
                            <td class=<?= ($team->qualifie) ? 'qualifie' : 'elimine'; ?>>
                                <?= $team->matchGagne; ?>
                            </td>
                            <td class=<?= ($team->qualifie) ? 'qualifie' : 'elimine'; ?>>
                                <?= $team->matchNull; ?>
                            </td>
                            <td class=<?= ($team->qualifie) ? 'qualifie' : 'elimine'; ?>>
                                <?= $team->matchPerdu; ?>
                            </td>
                            <td class=<?= ($team->qualifie) ? 'qualifie' : 'elimine'; ?>>
                                <?= $team->butPour; ?>
                            </td>
                            <td class=<?= ($team->qualifie) ? 'qualifie' : 'elimine'; ?>>
                                <?= $team->butContre; ?>
                            </td>
                            <td class=<?= ($team->qualifie) ? 'qualifie' : 'elimine'; ?>>
                                <?= $team->diffBut; ?>
                            </td>
                            <td class=<?= ($team->qualifie) ? 'qualifie' : 'elimine'; ?>>
                                <?= $team->points; ?>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>

            <!-- Les matchs de la demi finale -->
            <table border="1">
                <thead>
                    <tr>
                        <th>Demi Finale</th>
                        <th>Affiche</th>
                        <th>Score</th>
                        <th>Tir Au But</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $gameControlleur->initializeDemiFianlsGamesTeams('Demi Finale');
                    foreach ($gameView->listGamesDemiFinals() as $game) {
                    ?>
                        <tr>
                            <td>Match <?= $game->id; ?></td>
                            <td>
                                <div>
                                    <div>
                                        <div>
                                            <?= $game->equipe1->nom; ?>
                                        </div>
                                        <div>
                                            <img src=<?= $imageLink . $game->equipe1->drapeau; ?> alt=<?= $game->equipe1->nom; ?>>
                                        </div>
                                    </div>
                                    <span>VS</span>
                                    <div>
                                        <div>
                                            <img src=<?= $imageLink . $game->equipe2->drapeau; ?> alt=<?= $game->equipe2->nom; ?>>
                                        </div>
                                        <div>
                                            <?= $game->equipe2->nom; ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <form action="./championship.php" method="POST">
                                    <?php
                                    if (!$game->peutJouer && $game->termine) {
                                    ?>
                                        <input type="number" readonly value=<?= $game->scoreEquipe1; ?>>
                                        <input type="number" readonly value=<?= $game->scoreEquipe2; ?>>
                                        <input type="submit" class="termine" value="Terminé" disabled>
                                    <?php
                                    } elseif (!$game->peutJouer) {
                                    ?>
                                        <input type="number" readonly value=<?= $game->scoreEquipe1; ?>>
                                        <input type="number" readonly value=<?= $game->scoreEquipe2; ?>>
                                        <input type="submit" class="verouille" value="Vérouillé" disabled>
                                    <?php
                                    } else {
                                    ?>
                                        <input type="hidden" min="0" max="20" name="idMatch" value=<?= $game->id; ?>>
                                        <input type="number" min="0" max="20" name="scoreEquipe1" value=<?= $game->scoreEquipe1; ?>>
                                        <input type="number" min="0" max="20" name="scoreEquipe2" value=<?= $game->scoreEquipe2; ?>>
                                        <input type="submit" min="0" max="20" name="jouer" value="Jouer">
                                    <?php
                                    }
                                    ?>
                                </form>
                            </td>
                            <td>
                                <?php
                                if ($gameView->isDoneInPenalities($game->id)) {
                                ?>
                                    <div>
                                        <input type="number" name="pen_1" value=<?= $game->pen_1; ?> disabled>
                                        <input type="number" name="pen_2" value=<?= $game->pen_2; ?> disabled>
                                    </div>
                                <?php
                                }
                                ?>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>

            <?php
            if ($gameView->isPhaseDone('Demi Finale')) {
            ?>
                <!-- Les matchs de la petite finale -->
                <table border="1">
                    <thead>
                        <tr>
                            <th>Petite Finale</th>
                            <th>Affiche</th>
                            <th>Score</th>
                            <th>
                                Tir Au But
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $gameControlleur->initializeFinalsGamesTeams('Petite Finale');

                        foreach ($gameView->listGamesPetiteFinals() as $game) {
                        ?>
                            <tr>
                                <td>Match <?= $game->id; ?></td>
                                <td>
                                    <div>
                                        <div>
                                            <div>
                                                <?= $game->equipe1->nom; ?>
                                            </div>
                                            <div>
                                                <img src=<?= $imageLink . $game->equipe1->drapeau; ?> alt=<?= $game->equipe1->nom; ?>>
                                            </div>
                                        </div>
                                        <span>VS</span>
                                        <div>
                                            <div>
                                                <img src=<?= $imageLink . $game->equipe2->drapeau; ?> alt=<?= $game->equipe2->nom; ?>>
                                            </div>
                                            <div>
                                                <?= $game->equipe2->nom; ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <form action="./championship.php" method="POST">
                                        <?php
                                        if (!$game->peutJouer && $game->termine) {
                                        ?>
                                            <input type="number" readonly value=<?= $game->scoreEquipe1; ?>>
                                            <input type="number" readonly value=<?= $game->scoreEquipe2; ?>>
                                            <input type="submit" class="termine" value="Terminé" disabled>
                                        <?php
                                        } elseif (!$game->peutJouer) {
                                        ?>
                                            <input type="number" readonly value=<?= $game->scoreEquipe1; ?>>
                                            <input type="number" readonly value=<?= $game->scoreEquipe2; ?>>
                                            <input type="submit" class="verouille" value="Vérouillé" disabled>
                                        <?php
                                        } else {
                                        ?>
                                            <input type="hidden" min="0" max="20" name="idMatch" value=<?= $game->id; ?>>
                                            <input type="number" min="0" max="20" name="scoreEquipe1" value=<?= $game->scoreEquipe1; ?>>
                                            <input type="number" min="0" max="20" name="scoreEquipe2" value=<?= $game->scoreEquipe2; ?>>
                                            <input type="submit" min="0" max="20" name="jouer" value="Jouer">
                                        <?php
                                        }
                                        ?>
                                    </form>
                                </td>
                                <td>
                                    <?php
                                    if ($gameView->isDoneInPenalities($game->id)) {
                                    ?>
                                        <div>
                                            <input type="number" name="pen_1" value=<?= $game->pen_1; ?> disabled>
                                            <input type="number" name="pen_2" value=<?= $game->pen_2; ?> disabled>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>

                <!-- Les matchs de la grande finale -->
                <table border="1">
                    <thead>
                        <tr>
                            <th>Grande Finale</th>
                            <th>Affiche</th>
                            <th>Score</th>
                            <th>Tir Au But</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $gameControlleur->initializeFinalsGamesTeams('Grande Finale');
                        foreach ($gameView->listGamesGrandeFinals() as $game) {
                        ?>
                            <tr>
                                <td>Match <?= $game->id; ?></td>
                                <td>
                                    <div>
                                        <div>
                                            <div>
                                                <?= $game->equipe1->nom; ?>
                                            </div>
                                            <div>
                                                <img src=<?= $imageLink . $game->equipe1->drapeau; ?> alt=<?= $imageLink . $game->equipe1->nom; ?>>
                                            </div>
                                        </div>
                                        <span>VS</span>
                                        <div>
                                            <div>
                                                <img src=<?= $imageLink . $game->equipe2->drapeau; ?> alt=<?= $game->equipe2->nom; ?>>
                                            </div>
                                            <div>
                                                <?= $game->equipe2->nom; ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <form action="./championship.php" method="POST">
                                        <?php
                                        if (!$game->peutJouer && $game->termine) {
                                        ?>
                                            <input type="number" readonly value=<?= $game->scoreEquipe1; ?>>
                                            <input type="number" readonly value=<?= $game->scoreEquipe2; ?>>
                                            <input type="submit" class="termine" value="Terminé" disabled>
                                        <?php
                                        } elseif (!$game->peutJouer) {
                                        ?>
                                            <input type="number" readonly value=<?= $game->scoreEquipe1; ?>>
                                            <input type="number" readonly value=<?= $game->scoreEquipe2; ?>>
                                            <input type="submit" class="verouille" value="Vérouillé" disabled>
                                        <?php
                                        } else {
                                        ?>
                                            <input type="hidden" min="0" max="20" name="idMatch" value=<?= $game->id; ?>>
                                            <input type="number" min="0" max="20" name="scoreEquipe1" value=<?= $game->scoreEquipe1; ?>>
                                            <input type="number" min="0" max="20" name="scoreEquipe2" value=<?= $game->scoreEquipe2; ?>>
                                            <input type="submit" min="0" max="20" name="jouer" value="Jouer">
                                        <?php
                                        }
                                        ?>
                                    </form>
                                </td>
                                <td>
                                    <?php
                                    if ($gameView->isDoneInPenalities($game->id)) {
                                    ?>
                                        <div>
                                            <input type="number" name="pen_1" value=<?= $game->pen_1; ?> disabled>
                                            <input type="number" name="pen_2" value=<?= $game->pen_2; ?> disabled>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
                <?php

                if ($gameView->isPhaseDone('Grande Finale')) {
                    $thirdTeam = $gameView->getTeamWinner(15);
                    $championTeam = $gameView->getTeamWinner(16);
                    $secondTeam = $gameView->getTeamLooser(16);
                ?>
                    <section class="trophe">
                        <div class="trosieme">
                            <p>Troisième Place</p>
                            <img src=<?= $imageLink . $thirdTeam->drapeau ?> alt="">
                        </div>

                        <div class="première">
                            <p>Champion</p>
                            <img src="../css/images/world-cup.png" alt="">
                            <img src=<?= $imageLink . $championTeam->drapeau ?> alt="">
                        </div>

                        <div class="deuxième">
                            <p>Deuxième Place</p>
                            <img src=<?= $imageLink . $secondTeam->drapeau ?> alt="">
                        </div>
                    </section>
        <?php
                }
            }
        }
        ?>

        <!-- Le button d'annulation du tirage -->
        <form action="./championship.php" method="POST">
            <input type="submit" name="unDraw" value="Reset">
        </form>
    <?php
    }
    ?>
</body>

</html>