<?php

/**
 * La classe modèle d'équipe qui gère les récupérations 
 * ainsi que les modifications des données des matchs 
 * dans la base de données. 
 */
class GameModel extends Database
{
    //RÉCUPÉRATION DES DONNÉES
    protected function getGames()
    {
        $sql = 'SELECT * FROM game';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    protected function listeDemiFinalsGame()
    {
        $sql = 'SELECT * FROM game WHERE phase LIKE "Demi Finale"';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    protected function getWinner($idGame)
    {
        $sql = 'SELECT idEqG FROM game WHERE id = ?';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute([$idGame]);
        return $stmt->fetch();
    }

    protected function getLooser($idGame)
    {
        $sql = 'SELECT idEqP FROM game WHERE id = ?';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute([$idGame]);
        return $stmt->fetch();
    }

    protected function penalitiesIsNotNull($idGame)
    {
        $sql = 'SELECT * FROM game WHERE id = ? AND pen_1 IS NOT NULL AND pen_2 IS NOT NULL';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute([$idGame]);
        $stmt->fetchAll();
        return ($stmt->rowCount() > 0);
    }

    protected function getGamesByPhaseGroups($idGroup, $phase)
    {
        if ($idGroup == 1) {
            $sql = 'SELECT * FROM game WHERE phase LIKE ? AND id BETWEEN 1 AND 6';
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([$phase]);
            return $stmt->fetchAll();
        } elseif ($idGroup == 2) {
            $sql = 'SELECT * FROM game WHERE phase LIKE ? AND id BETWEEN 7 AND 12';
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([$phase]);
            return $stmt->fetchAll();
        } elseif ($idGroup == 3) {
            $sql = 'SELECT * FROM game WHERE phase LIKE ? AND id BETWEEN 13 AND 14';
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([$phase]);
            return $stmt->fetchAll();
        } elseif ($idGroup == 4) {
            $sql = 'SELECT * FROM game WHERE phase LIKE ? AND id = 15';
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([$phase]);
            return $stmt->fetchAll();
        } elseif ($idGroup == 5) {
            $sql = 'SELECT * FROM game WHERE phase LIKE ? AND id = 16';
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([$phase]);
            return $stmt->fetchAll();
        }
    }

    protected function getGamesByPhase($phase)
    {
        $sql = 'SELECT * FROM game WHERE phase LIKE ?';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute([$phase]);
        return $stmt->fetchAll();
    }

    protected function getNextGamesByNumber($previousGameNumber)
    {
        $sql = 'SELECT * FROM game WHERE numero = (? + 1)';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute([$previousGameNumber]);
        return $stmt->fetch();
    }

    protected function getGamesById($idGame)
    {
        $sql = 'SELECT * FROM game WHERE id = ?';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute([$idGame]);
        return $stmt->fetch();
    }

    protected function getNotDoneGamesPhase($phase)
    {
        $sql = 'SELECT * FROM game WHERE phase = ? AND termine = false';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute([$phase]);
        $stmt->fetchAll();
        return ($stmt->rowCount() > 0);
    }

    //MODIFICATION DES DONNÉES
    protected function initializeGameIdOfTeam($idFirstTeam, $idSecondTeam, $idGame)
    {
        $sql = 'UPDATE game SET idEquipe1 = ?, idEquipe2 = ? WHERE id = ?';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute([$idFirstTeam, $idSecondTeam, $idGame]);
    }

    protected function resetGames()
    {
        $sql = 'UPDATE game SET 
            idEquipe1 = NULL, 
            idEquipe2 = NULL, 
            scoreEquipe1 = NULL, 
            scoreEquipe2 = NULL, 
            peutJouer = false, 
            termine = false,
            idEqP = NULL,
            idEqG = NULL,
            pen_1 = NULL,
            pen_2 = NULL
        ';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute();
    }

    protected function updateGameTeamScore($idGame, $scoreFirstTeam, $scoreSecondTeam, $idTeamWin, $idTeamLose, $pen_1, $pen_2)
    {
        $sql = 'UPDATE game SET 
            scoreEquipe1 = ?, 
            scoreEquipe2 = ?, 
            idEqG = ?, 
            idEqP = ?, 
            peutJouer = false, 
            termine = true, 
            pen_1 = ?, 
            pen_2 = ? 
        WHERE id = ?';
        $stmt = $this->getConnection()->prepare($sql);
        if ($stmt->execute([$scoreFirstTeam, $scoreSecondTeam, $idTeamWin, $idTeamLose, $pen_1, $pen_2, $idGame])) {
            $currentGame =  $this->getGamesById($idGame);

            if ($currentGame->phase == 'Premier Tour') {
                $teamController = new TeamController();
                $teamController->playGame($currentGame->phase, $currentGame->idEquipe1, $currentGame->idEquipe2, $currentGame->scoreEquipe1, $currentGame->scoreEquipe2);
            }

            $currentGameNumber = $currentGame->numero;

            if ($currentGameNumber < 16) {
                $nextGame = $this->getNextGamesByNumber($currentGameNumber);
                $this->activeGame($nextGame->id);
            }
        }
    }

    protected function getGameInOrder($numberGame)
    {
        $sql = 'SELECT * FROM game WHERE numero = ?';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute([$numberGame]);
        return $stmt->fetch();
    }

    protected function activeGame($idGame)
    {
        $sql = 'UPDATE game SET peutJouer = true WHERE id = ?';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute([$idGame]);
    }
}
