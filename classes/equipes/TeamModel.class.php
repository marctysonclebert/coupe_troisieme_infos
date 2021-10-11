<?php

/**
 * La classe modèle d'équipe qui gère les récupérations 
 * ainsi que les modifications des données des équipes 
 * dans la base de données. 
 */
class TeamModel extends Database
{

    //RÉCUPÉRATION DES DONNÉES
    protected function getTeams()
    {
        $sql = 'SELECT * FROM team;';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    protected function getTeamById($id)
    {
        $sql = 'SELECT * FROM team WHERE id = ?;';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    protected function getTeamByIdLot($idLot, $id)
    {
        $sql = 'SELECT * FROM team WHERE idLot = ? AND id != ?;';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute([$idLot, $id]);
        return $stmt->fetch();
    }

    protected function getTeamsInLine($lineNumber)
    {
        if ($lineNumber == 1) {
            $sql = 'SELECT * FROM team WHERE id <= 4;';
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } elseif ($lineNumber == 2) {
            $sql = 'SELECT * FROM team WHERE id >= 5;';
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        }
    }

    protected function getClassementByGroup($idGroupe)
    {
        $sql = 'SELECT * FROM team WHERE 
                    idGroupe = ? ORDER BY 
                    points DESC,
                    diffBut DESC,
                    butPour  DESC,
                    butContre ASC,
                    idGroupe ASC 
                ';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute([$idGroupe]);
        return $stmt->fetchAll();
    }

    protected function teamsIdGroupNull()
    {
        $sql = 'SELECT * FROM team WHERE idGroupe IS NULL;';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute();
        return (count($stmt->fetchAll()) > 0);
    }


    protected function getGroup($idGroupe)
    {
        $sql = 'SELECT * FROM team
                WHERE idGroupe = ?
                ORDER BY idGroupe, idLot;';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute([$idGroupe]);
        return $stmt->fetchAll();
    }

    protected function getGroupTeamsQualify($idGroupe)
    {
        $sql = 'SELECT * FROM team WHERE
                    qualifie = true AND idGroupe = ?
                ORDER BY 
                    points DESC,
                    diffBut DESC,
                    butPour  DESC,
                    butContre ASC,
                    idGroupe ASC 
                ';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute([$idGroupe]);
        return $stmt->fetchAll();
    }

    //MODIFICATION DES DONNÉES
    protected function setTeamsIdGroupById($idGroupe, $id)
    {
        $sql = 'UPDATE team SET idGroupe = ? WHERE id = ?;';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute([$idGroupe, $id]);
    }

    protected function setTeamsIdGroupToNull()
    {
        $sql = 'UPDATE team SET idGroupe = NULL;';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute();
    }

    protected function updateGameTeams($gamePlayedTeam, $winTeam, $tieTeam, $loseTeam, $scoreForTeam, $scoreAgainstTeam, $diffScoreTeam, $pointsTeam, $qualifie, $idTeam)
    {
        $sql = 'UPDATE team SET matchJoue = ?, matchGagne = ?, matchNull = ?, matchPerdu = ?, butPour = ?, butContre = ?, diffBut = ?, points = ?, qualifie = ? WHERE id = ?';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute([$gamePlayedTeam, $winTeam, $tieTeam, $loseTeam, $scoreForTeam, $scoreAgainstTeam, $diffScoreTeam, $pointsTeam, $qualifie, $idTeam]);
    }

    protected function resetTeamGames()
    {
        $sql = 'UPDATE team SET matchJoue = NULL, matchGagne = NULL, matchNull = NULL, matchPerdu = NULL, butPour = NULL, butContre = NULL, diffBut = NULL, points = NULL, qualifie = false';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute();
    }
}
