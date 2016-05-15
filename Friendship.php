<?php

class Friendship
{
    private $requester_uuid, $recipient_uuid;
    private $invitation_date, $acceptation_date;
    private $status;

    public function __construct($requester_uuid, $recipient_uuid, $invitation_date, $acceptation_date, $status)
    {
        $this->requester_uuid = $requester_uuid;
        $this->recipient_uuid = $recipient_uuid;
        $this->invitation_date = $invitation_date;
        $this->acceptation_date = $acceptation_date;
        $this->status = boolval($status);
    }

    /**
     * @return string
     */
    public function getRequesterUuid()
    {
        return $this->requester_uuid;
    }

    /**
     * @return string
     */
    public function getRecipientUuid()
    {
        return $this->recipient_uuid;
    }

    /**
     * @return string
     */
    public function getInvitationDate()
    {
        return $this->invitation_date;
    }

    /**
     * @return string
     */
    public function getAcceptationDate()
    {
        return $this->acceptation_date;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->status;
    }
}