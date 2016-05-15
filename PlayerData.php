<?php

require_once('Exceptions.php');

abstract class PlayerData
{
    protected $db;
    protected $table_name;

    protected $uuid;

    protected $code_name;

    protected $row = null;

    protected $not_found = false;

    /**
     * @param $db PDO
     * @param $uuid string
     */
    public function __construct($db, $table_name, $code_name, $uuid)
    {
        $this->db = $db;
        $this->table_name = $table_name;
        $this->code_name = $code_name;
        $this->uuid = $uuid;
    }

    public function fetch()
    {
        if($this->row == null)
        {
            $stm = $this->db->prepare("SELECT * FROM $this->table_name WHERE uuid = UNHEX(replace(:uuid, '-', ''))");
            $stm->bindParam(':uuid', $this->uuid);
            $stm->execute();
            $fetch = $stm->fetchAll();

            if(count($fetch) == 1)
                $this->row = $fetch[0];
            else
            {
                $this->not_found = true;
                throw new PlayerStatisticsException();
            }
        }
    }

    /**
     * @return bool
     */
    public function hasNotFound()
    {
        return $this->not_found;
    }

    /**
     * @return bool
     */
    public function hasTried()
    {
        return $this->row == null;
    }

    /**
     * @return string
     */
    public function getCodeName()
    {
        return $this->code_name;
    }
}

abstract class PlayerGameStatistics extends PlayerData
{
    /**
     * @return string
     * @throws PlayerStatisticsException
     */
    public function getCreationDate()
    {
        $this->fetch();
        return $this->row['creation_date'];
    }

    /**
     * @return string
     * @throws PlayerStatisticsException
     */
    public function getUpdateDate()
    {
        $this->fetch();
        return $this->row['update_date'];
    }

    /**
     * @return int
     * @throws PlayerStatisticsException
     */
    public function getTimesPlayed()
    {
        $this->fetch();
        return intval($this->row['played_time']);
    }
}

abstract class PlayerWinnableGameStatistics extends PlayerGameStatistics
{
    /**
     * @return int
     * @throws PlayerStatisticsException
     */
    public function getPlayedGames()
    {
        $this->fetch();
        return intval($this->row['played_games']);
    }

    /**
     * @return int
     * @throws PlayerStatisticsException
     */
    public function getVictories()
    {
        $this->fetch();
        return intval($this->row['wins']);
    }
}

abstract class PlayerKillableGameStatistics extends PlayerWinnableGameStatistics
{
    /**
     * @return int
     * @throws PlayerStatisticsException
     */
    public function getDeaths()
    {
        $this->fetch();
        return intval($this->row['deaths']);
    }

    /**
     * @return int
     * @throws PlayerStatisticsException
     */
    public function getKills()
    {
        $this->fetch();
        return intval($this->row['kills']);
    }
}


// Games


class PlayerDimensionsStatistics extends PlayerKillableGameStatistics
{
    /**
     * @param PDO $db
     * @param $uuid string
     */
    public function __construct($db, $uuid)
    {
        parent::__construct($db, 'dimensions_stats', 'dimensions', $uuid);
    }
}

class PlayerHeroBattleStatistics extends PlayerKillableGameStatistics
{
    /**
     * @param PDO $db
     * @param $uuid string
     */
    public function __construct($db, $uuid)
    {
        parent::__construct($db, 'herobattle_stats', 'herobattle', $uuid);
    }

    /**
     * @return int
     * @throws PlayerStatisticsException
     */
    public function getElo()
    {
        $this->fetch();
        return intval($this->row['elo']);
    }

    /**
     * @return int
     * @throws PlayerStatisticsException
     */
    public function getPowerupTaken()
    {
        $this->fetch();
        return intval($this->row['powerup_taken']);
    }
}

class PlayerUpperVoidStatistics extends PlayerKillableGameStatistics
{
    /**
     * @param PDO $db
     * @param $uuid string
     */
    public function __construct($db, $uuid)
    {
        parent::__construct($db, 'uppervoid_stats', 'uppervoid', $uuid);
    }

    /**
     * @return int
     * @throws PlayerStatisticsException
     */
    public function getBlocks()
    {
        $this->fetch();
        return intval($this->row['blocks']);
    }

    /**
     * @return int
     * @throws PlayerStatisticsException
     */
    public function getGrenades()
    {
        $this->fetch();
        return intval($this->row['grenades']);
    }

    /**
     * @return int
     * @throws PlayerStatisticsException
     */
    public function getTNTLaunched()
    {
        $this->fetch();
        return intval($this->row['tnt_launched']);
    }
}

class PlayerQuakeStatistics extends PlayerKillableGameStatistics
{
    /**
     * @param PDO $db
     * @param $uuid string
     */
    public function __construct($db, $uuid)
    {
        parent::__construct($db, 'quake_stats', 'quake', $uuid);
    }
}

class PlayerUHCRunStatistics extends PlayerKillableGameStatistics
{
    /**
     * @param PDO $db
     * @param $uuid string
     */
    public function __construct($db, $uuid)
    {
        parent::__construct($db, 'uhcrun_stats', 'uhcrun', $uuid);
    }

    /**
     * @return int
     * @throws PlayerStatisticsException
     */
    public function getDamages()
    {
        $this->fetch();
        return intval($this->row['damages']);
    }

    /**
     * @return int
     * @throws PlayerStatisticsException
     */
    public function getMaxDamages()
    {
        $this->fetch();
        return intval($this->row['max_damages']);
    }
}

class PlayerJukeboxStatistics extends PlayerGameStatistics
{
    /**
     * @param PDO $db
     * @param $uuid string
     */
    public function __construct($db, $uuid)
    {
        parent::__construct($db, 'jukebox_stats', 'jukebox', $uuid);
    }

    /**
     * @return int
     * @throws PlayerStatisticsException
     */
    public function getMehs()
    {
        $this->fetch();
        return intval($this->row['mehs']);
    }

    /**
     * @return int
     * @throws PlayerStatisticsException
     */
    public function getWoots()
    {
        $this->fetch();
        return intval($this->row['woots']);
    }
}