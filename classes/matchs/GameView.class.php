<?php

/**
 * La classe vue de matchs qui gère
 *  les récupérations des données
 * dans la base de données. 
 */
class GameView extends GameModel
{

    public function listGames()
    {
        return $this->getGames();
    }

    public function listGamesA()
    {
        return $this->listGamesOfGroup(1, 'Premier Tour');;
    }

    public function listGamesB()
    {
        return $this->listGamesOfGroup(2, 'Premier Tour');
    }

    public function listGamesOfGroup($idGroup, $phase)
    {
        $teamView = new TeamView();

        return array_map(function ($game) use ($teamView) {
            return (object)[
                'id' => $game->id,
                'scoreEquipe1' => $game->scoreEquipe1,
                'scoreEquipe2' => $game->scoreEquipe2,
                'equipe1' => $teamView->getTeam($game->idEquipe1),
                'equipe2' => $teamView->getTeam($game->idEquipe2),
                'peutJouer' => $game->peutJouer,
                'termine' => $game->termine,
                'phase' => $game->phase,
                'pen_1' => $game->pen_1,
                'pen_2' => $game->pen_2
            ];
        }, $this->getGamesByPhaseGroups($idGroup, $phase));
    }

    public function isPhaseDone($phase)
    {
        return !($this->getNotDoneGamesPhase($phase));
    }

    public function listGamesDemiFinals()
    {
        return $this->listGamesOfGroup(3, 'Demi Finale');
    }

    public function listGamesPetiteFinals()
    {
        return $this->listGamesOfGroup(4, 'Petite Finale');
    }

    public function listGamesGrandeFinals()
    {
        return $this->listGamesOfGroup(5, 'Grande Finale');
    }

    public function isDoneInPenalities($idGame)
    {
        return $this->penalitiesIsNotNull($idGame);
    }

    public function getTeamWinner($idGame)
    {
        $teamView = new TeamView();
        return $teamView->getTeam($this->getWinner($idGame)->idEqG);
    }

    public function getTeamLooser($idGame)
    {
        $teamView = new TeamView();
        return $teamView->getTeam($this->getLooser($idGame)->idEqP);
    }
}
