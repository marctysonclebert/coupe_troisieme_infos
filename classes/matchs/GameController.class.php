<?php

/**
 * La classe controlleur de matchs qui gère
 *  les modifications des données dans 
 * la base de données. 
 */
class GameController extends GameModel
{
    public function initializeGamesTeams($phase)
    {
        $teamView = new TeamView();

        for ($i = 1; $i <= 2; $i++) {
            foreach ($this->getGamesByPhaseGroups($i, $phase) as $game) {
                $pos_1 = ($game->posEquipe_1 - 1);
                $pos_2 = ($game->posEquipe_2 - 1);

                if ($i == 1) {
                    $teamsGroup = $teamView->getGroupA();
                } elseif ($i == 2) {
                    $teamsGroup = $teamView->getGroupB();
                }

                $this->initializeGameIdOfTeam(
                    $teamsGroup[$pos_1]->id,
                    $teamsGroup[$pos_2]->id,
                    $game->id
                );
            }
        }
    }


    public function initializeDemiFianlsGamesTeams($phase)
    {
        $teamView = new TeamView();

        foreach ($this->getGamesByPhaseGroups(3, $phase) as $key => $game) {
            $pos_1 = ($game->posEquipe_1 - 1);
            $pos_2 = ($game->posEquipe_2 - 1);

            $teamsGroupA = $teamView->getGroupAQualify();
            $teamsGroupB = $teamView->getGroupBQualify();

            if ($key == 0) {
                $this->initializeGameIdOfTeam($teamsGroupA[$pos_1]->id, $teamsGroupB[$pos_2]->id, $game->id);
            } else {
                $this->initializeGameIdOfTeam($teamsGroupB[$pos_1]->id, $teamsGroupA[$pos_2]->id, $game->id);
            }
        }
    }

    public function initializeFinalsGamesTeams($phase)
    {
        $firstDemiFinal = $this->getGamesByPhaseGroups(3, 'Demi Finale')[0];
        $secondDemiFinal = $this->getGamesByPhaseGroups(3, 'Demi Finale')[1];

        if ($phase == 'Petite Finale') {
            $game = $this->getGamesByPhaseGroups(4, $phase);
            $this->initializeGameIdOfTeam($firstDemiFinal->idEqP, $secondDemiFinal->idEqP, $game[0]->id);
        } elseif ($phase == 'Grande Finale') {
            $game = $this->getGamesByPhaseGroups(5, $phase);
            $this->initializeGameIdOfTeam($firstDemiFinal->idEqG, $secondDemiFinal->idEqG, $game[0]->id);
        }
    }


    public function gamesReset()
    {
        $this->resetGames();
        $this->activeGame(1);
    }

    public function playGame($idGame, $scoreFirstTeam, $scoreSecondTeam)
    {
        $game = $this->getGamesById($idGame);

        $idTeamWin = NULL;
        $idTeamLose = NULL;

        $pen_1 = NULL;
        $pen_2 = NULL;

        if ($scoreFirstTeam > $scoreSecondTeam) {
            $idTeamWin = $game->idEquipe1;
            $idTeamLose = $game->idEquipe2;
        } elseif ($scoreFirstTeam < $scoreSecondTeam) {
            $idTeamWin = $game->idEquipe2;
            $idTeamLose = $game->idEquipe1;
        } else {
            if (($game->phase == 'Demi Finale')
                || ($game->phase == 'Petite Finale')
                || ($game->phase == 'Grande Finale')
            ) {
                do {
                    $pen_1 = rand(0, 3);
                    $pen_2 = rand(0, 3);
                } while ($pen_1 == $pen_2);

                if ($pen_1 > $pen_2) {
                    $idTeamWin = $game->idEquipe1;
                    $idTeamLose = $game->idEquipe2;
                } else {
                    $idTeamWin = $game->idEquipe2;
                    $idTeamLose = $game->idEquipe1;
                }
            } else {
                $idTeamWin = NULL;
                $idTeamLose = NULL;
            }
        }

        $this->updateGameTeamScore($idGame, $scoreFirstTeam, $scoreSecondTeam, $idTeamWin, $idTeamLose, $pen_1, $pen_2);
    }
}
