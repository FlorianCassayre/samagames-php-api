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
        if(!$this->not_found && $this->row == null)
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
        return $this->row == null && $this->not_found || $this->row != null;
    }

    protected function getValue($key, $default)
    {
        $this->fetch();

        if($this->hasNotFound() || !isset($this->row[$key]))
            return $default;
        return $this->row[$key];
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
        return $this->getValue('creation_date', null);
    }

    /**
     * @return string
     * @throws PlayerStatisticsException
     */
    public function getUpdateDate()
    {
        return $this->getValue('update_date', null);
    }

    /**
     * @return int
     * @throws PlayerStatisticsException
     */
    public function getTimesPlayed()
    {
        return intval($this->getValue('played_time', 0));
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
        return intval($this->getValue('played_games', 0));
    }

    /**
     * @return int
     * @throws PlayerStatisticsException
     */
    public function getVictories()
    {
        return intval($this->getValue('wins', 0));
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
        return intval($this->getValue('deaths', 0));
    }

    /**
     * @return int
     * @throws PlayerStatisticsException
     */
    public function getKills()
    {
        return intval($this->getValue('kills', 0));
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
        return intval($this->getValue('elo', 0));
    }

    /**
     * @return int
     * @throws PlayerStatisticsException
     */
    public function getPowerupTaken()
    {
        return intval($this->getValue('powerup_taken', 0));
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
        return intval($this->getValue('blocks', 0));
    }

    /**
     * @return int
     * @throws PlayerStatisticsException
     */
    public function getGrenades()
    {
        return intval($this->getValue('grenades', 0));
    }

    /**
     * @return int
     * @throws PlayerStatisticsException
     */
    public function getTNTLaunched()
    {
        return intval($this->getValue('tnt_launched', 0));
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
        return intval($this->getValue('damages', 0));
    }

    /**
     * @return int
     * @throws PlayerStatisticsException
     */
    public function getMaxDamages()
    {
        return intval($this->getValue('max_damages', 0));
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
        return intval($this->getValue('mehs', 0));
    }

    /**
     * @return int
     * @throws PlayerStatisticsException
     */
    public function getWoots()
    {
        return intval($this->getValue('woots', 0));
    }
}