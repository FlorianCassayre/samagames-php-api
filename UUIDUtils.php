<?php


function addDashes($uuid)
{
    return substr($uuid, 0, 8) . '-' . substr($uuid, 8, 4) . '-' . substr($uuid, 12, 4) . '-' . substr($uuid, 16, 4)  . '-' . substr($uuid, 20);
}

function isUUID($str)
{
    $allowed = '0123456789abcdef';
    $chars = str_split($str);

    if(strlen($str) == 32)
    {
        // UUID without dashes

        foreach($chars as $char)
        {
            if (strpos($allowed, $char) !== FALSE)
            {
                // Ok
            }
            else
            {
                return false;
            }
        }

        return true;
    }
    elseif(strlen($str) == 32 + 4)
    {
        // UUID with dashes

        $i = 0;

        foreach($chars as $char)
        {
            if($i == 8 || $i == 13 || $i == 18 || $i == 23) // 8 13 18 23
            {
                if(!($char === '-'))
                    return false;
            }
            else
            {
                if (strpos($allowed, $char) !== FALSE)
                {

                }
                else
                {
                    return false;
                }
            }

            $i++;
        }

        return true;
    }
    else
        return false;
}

function getNameOrUUID($str)
{
    $is_success = true;
    $fail_message = '';

    if(isUUID($str))
    {
        $player_uuid = str_replace('-', '', $str);

        $result = file_get_contents('https://api.mojang.com/user/profiles/' . $player_uuid . '/names');

        $player_uuid = addDashes($player_uuid);

        $result_json = json_decode($result, true);

        if(count($result_json) >= 1)
        {
            foreach($result_json as $change)
            {
                if($change == end($result_json))
                    $player_name = $change['name'];
            }
        }
        else
        {
            $is_success = false;

            $fail_message = 'uuid_not_found';
        }
    }
    else
    {
        if(strlen($str) >= 3 && strlen($str) <= 16)
        {
            $player_name = $str;

            $result = file_get_contents('https://api.mojang.com/users/profiles/minecraft/' . $player_name);

            $result_json = json_decode($result);

            if(isset($result_json->id))
            {
                $player_uuid = addDashes($result_json->id);

                $player_name = $result_json->name;
            }
            else
            {
                $is_success = false;

                $fail_message = 'name_not_found';
            }
        }
        else
        {
            $is_success = false;

            $fail_message = 'not_recognized';
        }
    }

    if($is_success)
    {
        $obj = array('success' => true, 'name' => $player_name, 'uuid' => $player_uuid);
    }
    else
    {
        $obj = array('success' => false, 'message' => $fail_message);
    }

    return json_encode($obj);
}