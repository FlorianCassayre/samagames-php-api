<?php

require_once('Player.php');
require_once('GroupsManager.php');
require_once('Friendship.php');
require_once('Sanction.php');
require_once('PlayerData.php');

class SamaGamesPlayer extends Player
{

    /** @var PDO  */
    private $db;


    private $group_id;
    private $coins, $stars;
    private $last_login, $first_login;
    private $nickname;
    private $last_ip;

    private $group;

    private $friends = null;

    private $sanctions = null;

    /** @var PlayerHeroBattleStatistics */
    private $herobattleStatistics;
    /** @var PlayerJukeboxStatistics */
    private $jukeboxStatistics;
    /** @var PlayerQuakeStatistics */
    private $quakeStatistics;
    /** @var PlayerUHCRunStatistics */
    private $uhcrunStatistics;
    /** @var PlayerUpperVoidStatistics */
    private $uppervoidStatistics;
    /** @var PlayerDimensionsStatistics */
    private $dimensionsStatistics;


    /**
     * @param $name_or_uuid string
     * @param $host string
     * @param $port int
     * @param $database_name string
     * @param $user string
     * @param $password string
     * @throws UnknownInputException
     * @throws UnknownNameException
     * @throws UnknownUUIDException
     * @throws UnknownSamaGamesPlayerException
     */
    public function __construct($name_or_uuid, $host, $port, $database_name, $user, $password)
    {
        parent::__construct($name_or_uuid);

        try {
            $this->db = new PDO('mysql:host=' . $host . ';dbname=' . $database_name . ';port=' . $port, $user, $password);

            $this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $ex) {
            echo $ex->getMessage();
            die();
        }

        $stm = $this->db->prepare("SELECT * FROM players WHERE uuid = UNHEX(replace(:uuid, '-', ''))");
        $stm->bindParam(':uuid', $this->uuid);
        $stm->execute();
        $fetch = $stm->fetchAll();

        if(count($fetch) == 1)
        {
            foreach($fetch as $row)
            {
                $this->group_id = intval($row['group_id']);
                $this->coins = intval($row['coins']);
                $this->stars = intval($row['stars']);
                $this->last_login = $row['last_login'];
                $this->first_login = $row['first_login'];
                $this->nickname = $row['nickname'];
                $this->last_ip = $row['last_ip'];
            }
        }
        else
        {
            throw new UnknownSamaGamesPlayerException();
        }

        $groupsManager = new GroupsManager($this->db);

        $this->group = $groupsManager->getGroup($this->group_id);


        $this->herobattleStatistics = new PlayerHeroBattleStatistics($this->db, $this->uuid);
        $this->jukeboxStatistics = new PlayerJukeboxStatistics($this->db, $this->uuid);
        $this->quakeStatistics = new PlayerQuakeStatistics($this->db, $this->uuid);
        $this->uhcrunStatistics = new PlayerUHCRunStatistics($this->db, $this->uuid);
        $this->uppervoidStatistics = new PlayerUpperVoidStatistics($this->db, $this->uuid);
        $this->dimensionsStatistics = new PlayerDimensionsStatistics($this->db, $this->uuid);
    }

    /**
     * @return int
     */
    public function getGroupId()
    {
        return intval($this->group_id);
    }

    /**
     * @return int
     */
    public function getCoins()
    {
        return intval($this->coins);
    }

    /**
     * @return int
     */
    public function getStars()
    {
        return intval($this->stars);
    }

    /**
     * @return string
     */
    public function getLastLogin()
    {
        return $this->last_login;
    }

    /**
     * @return string
     */
    public function getFirstLogin()
    {
        return $this->first_login;
    }

    /**
     * @return string
     */
    public function getNickname()
    {
        return $this->nickname;
    }

    /**
     * @return string
     */
    public function getLastIp()
    {
        return $this->last_ip;
    }

    /**
     * @return Group
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param $packed string
     * @return string
     */
    private function unpackUUID($packed)
    {
        $lower = strtolower($packed);
        $uuid = addDashes($lower);
        return $uuid;
    }

    /**
     * @return Friendship[]
     */
    public function getFriends()
    {
        if($this->friends == null)
        {
            $this->friends = array();

            $stm = $this->db->prepare("SELECT friendship_id, HEX(requester_uuid) AS requester_uuid, HEX(recipient_uuid) AS recipient_uuid, demand_date, acceptation_date, active_status FROM friendship WHERE requester_uuid = UNHEX(replace(:uuid1, '-', '')) OR recipient_uuid = UNHEX(replace(:uuid2, '-', ''))");
            $stm->bindParam(':uuid1', $this->uuid);
            $stm->bindParam(':uuid2', $this->uuid);
            $stm->execute();
            $fetch = $stm->fetchAll();

            foreach ($fetch as $row)
            {
                $friendship = new Friendship($this->unpackUUID($row['requester_uuid']), $this->unpackUUID($row['recipient_uuid']), $row['demand_date'], $row['acceptation_date'], $row['active_status']);

                array_push($this->friends, $friendship);
            }
        }

        return $this->friends;
    }

    /**
     * @return Sanction[]
     */
    public function getSanctions()
    {
        if($this->sanctions == null)
        {
            $this->sanctions = array();

            $stm = $this->db->prepare("SELECT sanction_id, HEX(player_uuid) AS player_uuid, type_id, reason, HEX(punisher_uuid) AS punisher_uuid, expiration_date, is_deleted, creation_date, update_date FROM sanctions WHERE player_uuid = UNHEX(replace(:uuid, '-', ''))");
            $stm->bindParam(':uuid', $this->uuid);
            $stm->execute();
            $fetch = $stm->fetchAll();

            foreach ($fetch as $row)
            {
                $sanction = new Sanction($this->unpackUUID($row['player_uuid']), intval($row['type_id']), $row['reason'], $this->unpackUUID($row['punisher_uuid']), $row['creation_date'], $row['expiration_date'], $row['update_date'], boolval($row['is_deleted']));

                array_push($this->sanctions, $sanction);
            }
        }

        return $this->sanctions;
    }

    /**
     * @return PlayerHeroBattleStatistics
     */
    public function getHeroBattleStatistics()
    {
        if(!$this->herobattleStatistics->hasTried())
        {
            try
            {
                $this->herobattleStatistics->fetch();
            }
            catch(PlayerStatisticsException $ex)
            {
                return null;
            }
        }

        if($this->herobattleStatistics->hasNotFound())
        {
            return null;
        }

        return $this->herobattleStatistics;
    }

    /**
     * @return null|PlayerJukeboxStatistics
     */
    public function getJukeboxStatistics()
    {
        if(!$this->jukeboxStatistics->hasTried())
        {
            try
            {
                $this->jukeboxStatistics->fetch();
            }
            catch(PlayerStatisticsException $ex)
            {
                return null;
            }
        }

        if($this->jukeboxStatistics->hasNotFound())
        {
            return null;
        }

        return $this->jukeboxStatistics;
    }

    /**
     * @return null|PlayerQuakeStatistics
     */
    public function getQuakeStatistics()
    {
        if(!$this->quakeStatistics->hasTried())
        {
            try
            {
                $this->quakeStatistics->fetch();
            }
            catch(PlayerStatisticsException $ex)
            {
                return null;
            }
        }

        if($this->quakeStatistics->hasNotFound())
        {
            return null;
        }

        return $this->quakeStatistics;
    }

    /**
     * @return null|PlayerUHCRunStatistics
     */
    public function getUHCRunStatistics()
    {
        if(!$this->uhcrunStatistics->hasTried())
        {
            try
            {
                $this->uhcrunStatistics->fetch();
            }
            catch(PlayerStatisticsException $ex)
            {
                return null;
            }
        }

        if($this->uhcrunStatistics->hasNotFound())
        {
            return null;
        }

        return $this->uhcrunStatistics;
    }

    /**
     * @return null|PlayerUpperVoidStatistics
     */
    public function getUpperVoidStatistics()
    {
        if(!$this->uppervoidStatistics->hasTried())
        {
            try
            {
                $this->uppervoidStatistics->fetch();
            }
            catch(PlayerStatisticsException $ex)
            {
                return null;
            }
        }

        if($this->uppervoidStatistics->hasNotFound())
        {
            return null;
        }

        return $this->uppervoidStatistics;
    }

    /**
     * @return null|PlayerDimensionsStatistics
     */
    public function getDimensionsStatistics()
    {
        if(!$this->dimensionsStatistics->hasTried())
        {
            try
            {
                $this->dimensionsStatistics->fetch();
            }
            catch(PlayerStatisticsException $ex)
            {
                return null;
            }
        }

        if($this->dimensionsStatistics->hasNotFound())
        {
            return null;
        }

        return $this->dimensionsStatistics;
    }
}