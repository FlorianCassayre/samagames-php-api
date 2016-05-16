<?php

require_once('Player.php');
require_once('GroupsManager.php');
require_once('Friendship.php');
require_once('Sanction.php');
require_once('PlayerData.php');
require_once('ShopRef.php');
require_once('Transaction.php');

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

    /** @var ShopRef */
    private $shopRefs = null;
    /** @var Transaction  */
    private $shopTransactions = null;

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

        $stm = $this->db->prepare("SELECT * FROM players WHERE uuid = UNHEX(REPLACE(:uuid, '-', ''))");
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
        return $this->nickname === 'null' ? null : $this->nickname;
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

            $stm = $this->db->prepare("SELECT friendship_id, HEX(requester_uuid) AS requester_uuid, HEX(recipient_uuid) AS recipient_uuid, demand_date, acceptation_date, active_status FROM friendship WHERE requester_uuid = UNHEX(REPLACE(:uuid1, '-', '')) OR recipient_uuid = UNHEX(REPLACE(:uuid2, '-', ''))");
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

            $stm = $this->db->prepare("SELECT sanction_id, HEX(player_uuid) AS player_uuid, type_id, reason, HEX(punisher_uuid) AS punisher_uuid, expiration_date, is_deleted, creation_date, update_date FROM sanctions WHERE player_uuid = UNHEX(REPLACE(:uuid, '-', ''))");
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
        return $this->herobattleStatistics;
    }

    /**
     * @return PlayerJukeboxStatistics
     */
    public function getJukeboxStatistics()
    {
        return $this->jukeboxStatistics;
    }

    /**
     * @return PlayerQuakeStatistics
     */
    public function getQuakeStatistics()
    {
        return $this->quakeStatistics;
    }

    /**
     * @return PlayerUHCRunStatistics
     */
    public function getUHCRunStatistics()
    {
        return $this->uhcrunStatistics;
    }

    /**
     * @return PlayerUpperVoidStatistics
     */
    public function getUpperVoidStatistics()
    {
        return $this->uppervoidStatistics;
    }

    /**
     * @return PlayerDimensionsStatistics
     */
    public function getDimensionsStatistics()
    {
        return $this->dimensionsStatistics;
    }

    /**
     * @return Transaction[]
     */
    public function getTransactions()
    {
        if($this->shopRefs == null)
        {
            $this->shopRefs = array();

            $stm = $this->db->prepare("SELECT * FROM item_description");
            $stm->execute();
            $fetch = $stm->fetchAll();

            foreach($fetch as $row)
            {
                $id = intval($row['item_id']);

                $itemDescription = new ShopRef($id, $row['item_name'], $row['item_desc'], $row['item_rarity'], $row['item_minecraft_id'], intval($row['price_coins']), intval($row['price_stars']), $row['rank_accessibility'], intval($row['game_category']));

                $this->shopRefs[$id] = $itemDescription;
            }
        }

        if($this->shopTransactions == null)
        {
            $this->shopTransactions = array();

            $stm = $this->db->prepare("SELECT * FROM transaction_shop WHERE uuid_buyer = UNHEX(REPLACE(:uuid, '-', ''))");
            $stm->bindParam(':uuid', $this->uuid);
            $stm->execute();
            $fetch = $stm->fetchAll();

            foreach ($fetch as $row)
            {
                $transaction = new Transaction(intval($row['transaction_id']), $this->shopRefs[intval($row['item_id'])], intval($row['price_coins']), intval($row['price_stars']), $row['transaction_date'], boolval($row['selected']));

                array_push($this->shopTransactions, $transaction);
            }
        }

        return $this->shopTransactions;
    }
}