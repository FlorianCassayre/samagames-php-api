<?php

require_once('Exceptions.php');
require_once('UUIDUtils.php');

class Player {

    protected $name;
    protected $uuid;

    public function __construct($name_or_uuid)
    {
        $result = json_decode(getNameOrUUID($name_or_uuid));

        if($result->success)
        {
            $this->name = $result->name;
            $this->uuid = $result->uuid;
        }
        else
        {
            if($result->message === 'uuid_not_found')
            {
                throw new UnknownUUIDException();
            }
            else if($result->message === 'name_not_found')
            {
                throw new UnknownNameException();
            }
            else
            {
                throw new UnknownInputException();
            }
        }
    }

    public function getUUIDWithDashes()
    {
        return $this->uuid;
    }

    public function getUUIDWithoutDashes()
    {
        return str_replace('-', '', $this->uuid);
    }

    public function getName()
    {
        return $this->name;
    }
}