<?php

/**
 * La classe controlleur d'équipe qui gère
 *  les modifications des données dans 
 * la base de données. 
 */
class TeamController extends TeamModel
{
    public function draw()
    {
        if (rand(0, 1) == 0) {
            foreach ($this->getTeamsInLine(1) as $firstTeam) {
                $secondTeam = $this->getTeamByIdLot($firstTeam->idLot, $firstTeam->id);

                $sameTdsTeams = [$firstTeam, $secondTeam];

                if ($sameTdsTeams[rand(0, 1)] == $sameTdsTeams[0]) {
                    $this->setTeamsIdGroupById(1, $sameTdsTeams[0]->id);
                    $this->setTeamsIdGroupById(2, $sameTdsTeams[1]->id);
                } else {
                    $this->setTeamsIdGroupById(2, $sameTdsTeams[0]->id);
                    $this->setTeamsIdGroupById(1, $sameTdsTeams[1]->id);
                }
            }
        } else {
            foreach ($this->getTeamsInLine(2) as $firstTeam) {
                $secondTeam = $this->getTeamByIdLot($firstTeam->idLot, $firstTeam->id);

                $sameTdsTeams = [$firstTeam, $secondTeam];

                if ($sameTdsTeams[rand(0, 1)] == $sameTdsTeams[0]) {
                    $this->setTeamsIdGroupById(1, $sameTdsTeams[0]->id);
                    $this->setTeamsIdGroupById(2, $sameTdsTeams[1]->id);
                } else {
                    $this->setTeamsIdGroupById(2, $sameTdsTeams[0]->id);
                    $this->setTeamsIdGroupById(1, $sameTdsTeams[1]->id);
                }
            }
        }
    }

    public function unDraw()
    {
        $this->setTeamsIdGroupToNull();
        $this->resetTeamGames();
    }

    public function playGame($phase, $idFirstTeam, $idSecondTeam, $scoreFirstTeam, $scoreSecondTeam)
    {
        $firstTeam = $this->getTeamById($idFirstTeam);
        $secondTeam = $this->getTeamById($idSecondTeam);

        $gamePlayedFirstTeam = $firstTeam->matchJoue + 1;
        $gamePlayedSecondTeam = $secondTeam->matchJoue + 1;

        $tieFirstTeam = $firstTeam->matchNull;
        $tieSecondTeam = $secondTeam->matchNull;

        $winFirstTeam = $firstTeam->matchGagne;
        $winSecondTeam = $secondTeam->matchGagne;

        $loseFirstTeam = $firstTeam->matchPerdu;
        $loseSecondTeam = $secondTeam->matchPerdu;

        $pointsFirstTeam = $firstTeam->points;
        $pointsSecondTeam = $secondTeam->points;

        $qualifyFirstTeam = false;
        $qualifySecondTeam = false;

        if ($scoreFirstTeam == $scoreSecondTeam) {
            $tieFirstTeam += 1;
            $tieSecondTeam += 1;

            $winFirstTeam += 0;
            $winSecondTeam += 0;

            $loseFirstTeam += 0;
            $loseSecondTeam += 0;

            $pointsFirstTeam += 1;
            $pointsSecondTeam += 1;

            if ($phase == 'Demi Finale') {
                $qualifyFirstTeam = false;
                $qualifySecondTeam = false;
            }

        } elseif ($scoreFirstTeam > $scoreSecondTeam) {
            $tieFirstTeam += 0;
            $tieSecondTeam += 0;

            $winFirstTeam += 1;
            $winSecondTeam += 0;

            $loseFirstTeam += 0;
            $loseSecondTeam += 1;

            $pointsFirstTeam += 3;
            $pointsSecondTeam += 0;

            if ($phase == 'Demi Finale') {
                $qualifyFirstTeam = true;
                $qualifySecondTeam = false;
            }
        } else {
            $tieFirstTeam += 0;
            $tieSecondTeam += 0;

            $winFirstTeam += 0;
            $winSecondTeam += 1;

            $loseFirstTeam += 1;
            $loseSecondTeam += 0;

            $pointsFirstTeam += 0;
            $pointsSecondTeam += 3;

            if ($phase == 'Demi Finale') {
                $qualifyFirstTeam = false;
                $qualifySecondTeam = true;
            }
        }

        $scoreForFirstTeam = ($firstTeam->butPour + $scoreFirstTeam);
        $scoreAgainstFirstTeam = ($firstTeam->butContre + $scoreSecondTeam);

        $scoreForSecondTeam = ($secondTeam->butPour + $scoreSecondTeam);
        $scoreAgainstSecondTeam = ($secondTeam->butContre + $scoreFirstTeam);

        $diffScoreFirstTeam = ($scoreForFirstTeam - $scoreAgainstFirstTeam);
        $diffScoreSecondTeam = ($scoreForSecondTeam - $scoreAgainstSecondTeam);


        $this->updateGameTeams(
            $gamePlayedFirstTeam,
            $winFirstTeam,
            $tieFirstTeam,
            $loseFirstTeam,
            $scoreForFirstTeam,
            $scoreAgainstFirstTeam,
            $diffScoreFirstTeam,
            $pointsFirstTeam,
            $qualifyFirstTeam,
            $idFirstTeam
        );
        $this->updateGameTeams(
            $gamePlayedSecondTeam,
            $winSecondTeam,
            $tieSecondTeam,
            $loseSecondTeam,
            $scoreForSecondTeam,
            $scoreAgainstSecondTeam,
            $diffScoreSecondTeam,
            $pointsSecondTeam,
            $qualifySecondTeam,
            $idSecondTeam
        );
    }

    public function setTeamQualify()
    {
        for($i = 1; $i <= 2; $i++) { 
            $classementGroups = $this->getClassementByGroup($i);

            foreach($classementGroups as $key => $team) {
                if ($key <= 1) {
                    $sql = 'UPDATE team SET qualifie = ? WHERE id = ?';
                    $stmt = $this->getConnection()->prepare($sql);
                    $stmt->execute([true, $team->id]);
                }else{
                    $sql = 'UPDATE team SET qualifie = ? WHERE id = ?';
                    $stmt = $this->getConnection()->prepare($sql);
                    $stmt->execute([false, $team->id]);
                }
            }
        }
    }
}
