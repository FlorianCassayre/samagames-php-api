<?php

class Sanction
{
    private $player_uuid;
    private $type_id, $reason;
    private $punisher_uuid;
    private $creation_date, $expiration_date, $update_date;
    private $is_deleted;

    const WARNING = 1;
    const BAN = 2;
    const KICK = 3;
    const MUTE = 4;
    const TEXT = 5;

    const SAMARITAN_UUID = '00000000-0000-0000-0000-000000000000';

    /**
     * @param $player_uuid string
     * @param $type_id int
     * @param $reason string
     * @param $punisher_uuid string
     * @param $creation_date string
     * @param $expiration_date string
     * @param $update_date string
     * @param $is_deleted bool
     */
    public function __construct($player_uuid, $type_id, $reason, $punisher_uuid, $creation_date, $expiration_date, $update_date, $is_deleted)
    {
        $this->player_uuid = $player_uuid;
        $this->type_id = $type_id;
        $this->reason = $reason;
        $this->punisher_uuid = $punisher_uuid;
        $this->creation_date = $creation_date;
        $this->expiration_date = $expiration_date;
        $this->update_date = $update_date;
        $this->is_deleted = $is_deleted;
    }

    /**
     * @return string
     */
    public function getPlayerUuid()
    {
        return $this->player_uuid;
    }

    /**
     * @return int
     */
    public function getTypeId()
    {
        return $this->type_id;
    }

    /**
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * @return string
     */
    public function getPunisherUuid()
    {
        return $this->punisher_uuid;
    }

    /**
     * @return string
     */
    public function getCreationDate()
    {
        return $this->creation_date;
    }

    /**
     * @return string
     */
    public function getExpirationDate()
    {
        return $this->expiration_date;
    }

    /**
     * @return string
     */
    public function getUpdateDate()
    {
        return $this->update_date;
    }

    /**
     * @return bool
     */
    public function getIsDeleted()
    {
        return $this->is_deleted;
    }

    /**
     * @return bool
     */
    public function isSamaritanSanction()
    {
        return $this->punisher_uuid === Sanction::SAMARITAN_UUID;
    }
}