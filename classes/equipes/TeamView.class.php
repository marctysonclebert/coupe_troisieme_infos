<?php

/**
 * La classe vue d'équipe qui gère
 *  les récupérations des données
 * dans la base de données. 
 */
class TeamView extends TeamModel
{
    public function teamsList()
    {
        return $this->getTeams();
    }

    public function getTeam($id)
    {
        return $this->getTeamById($id);
    }

    public function getClassementOfGroup($groupe)
    {
        return $this->getClassementByGroup($groupe);
    }

    public function firstLineTeams()
    {
        return $this->getTeamsInLine(1);
    }

    public function secondLineTeams()
    {
        return $this->getTeamsInLine(2);
    }

    public function canMakeDraw()
    {
        return $this->teamsIdGroupNull();
    }

    public function getGroupA()
    {
        return $this->getGroup(1);
    }

    public function getGroupB()
    {
        return $this->getGroup(2);
    }

    public function getGroupAQualify()
    {
        return $this->getGroupTeamsQualify(1);
    }

    public function getGroupBQualify()
    {
        return $this->getGroupTeamsQualify(2);
    }

    public function anyTeamQualified()
    {
        if (!(count($this->getGroupAQualify()) > 0) && !(count($this->getGroupBQualify()) > 0)) {
            return false;
        } else {
            return true;
        }
    }
}
