<?php

header('Content-Type: application/json; charset=utf-8');

require_once('SamaGamesPlayer.php');


try
{
    $object = array();

    try
    {
        $input = isset($_GET['input']) ? $_GET['input'] : '';

        $player = new SamaGamesPlayer($input, 'host', 3306, 'samagames', 'root', 'password'); // TODO fill with credentials
    }
    catch(Exception $ex)
    {
        $code = '';
        if($ex instanceof UnknownInputException)
        {
            $code = 'unknown_input';
        }
        elseif($ex instanceof UnknownUUIDException)
        {
            $code = 'unknown_uuid';
        }
        elseif($ex instanceof UnknownNameException)
        {
            $code = 'unknown_name';
        }
        elseif($ex instanceof UnknownSamaGamesPlayerException)
        {
            $code = 'unknown_samagames_player';
        }

        $object['success'] = false;
        $object['code'] = $code;

        $json = json_encode((object) $object, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        echo $json;

        die();
    }

    $object['success'] = true;

    $object['name'] = $player->getName();
    $object['uuid'] = $player->getUUIDWithDashes();
    $object['uuid_without'] = $player->getUUIDWithoutDashes();
    $object['coins'] = $player->getCoins();
    $object['stars'] = $player->getStars();
    $object['first_login'] = $player->getFirstLogin();
    $object['last_login'] = $player->getLastLogin();
    $object['nickname'] = $player->getNickname();
    $object['last_ip'] = $player->getLastIp();

    $object['group_id'] = $player->getGroupId();
    $object['group_name'] = $player->getGroup()->getName();
    $object['tag'] = utf8_encode($player->getGroup()->getTag());
    $object['tag_raw'] = utf8_encode($player->getGroup()->getTagRaw());
    $object['rank'] = $player->getGroup()->getRank();
    $object['prefix'] = $player->getGroup()->getPrefix();
    $object['suffix'] = $player->getGroup()->getSuffix();
    $object['multiplier'] = $player->getGroup()->getMultiplier();


    $friends = array();

    foreach ($player->getFriends() as $friend)
    {
        $item = array();

        $item['requester_uuid'] = $friend->getRequesterUuid();
        $item['recipient_uuid'] = $friend->getRecipientUuid();
        $item['invitation_date'] = $friend->getInvitationDate();
        $item['acceptation_date'] = $friend->getAcceptationDate();
        $item['is_active'] = $friend->isActive();

        array_push($friends, (object) $item);
    }

    $object['friends'] = $friends;


    $sanctions = array();

    foreach($player->getSanctions() as $sanction)
    {
        $item = array();

        $item['is_deleted'] = $sanction->getIsDeleted();
        $item['type_id'] = $sanction->getTypeId();
        $item['reason'] = utf8_encode($sanction->getReason());
        $item['punisher_uuid'] = $sanction->getPunisherUuid();
        $item['creation_date'] = $sanction->getCreationDate();
        $item['expiration_date'] = $sanction->getExpirationDate();
        $item['update_date'] = $sanction->getUpdateDate();
        $item['is_samaritan'] = $sanction->isSamaritanSanction();

        array_push($sanctions, (object) $item);
    }


    $object['sanctions'] = $sanctions;


    $statistics = array();

    $games = array($player->getJukeboxStatistics(), $player->getHeroBattleStatistics(), $player->getQuakeStatistics(), $player->getUHCRunStatistics(), $player->getUpperVoidStatistics(), $player->getUpperVoidStatistics(), $player->getDimensionsStatistics()); // , $player->getUpperVoidStatistics(), $player->getDimensionsStatistics()

    foreach ($games as $game)
    {
        $item = array();

        if($game instanceof PlayerGameStatistics)
        {
            $item['creation_date'] = $game->getCreationDate();
            $item['update_date'] = $game->getUpdateDate();
            $item['played_time'] = $game->getTimesPlayed();

            if($game instanceof PlayerKillableGameStatistics)
            {
                $item['played_games'] = $game->getPlayedGames();
                $item['wins'] = $game->getVictories();
                $item['kills'] = $game->getKills();
                $item['deaths'] = $game->getDeaths();

                if($game instanceof PlayerDimensionsStatistics)
                {

                }
                elseif($game instanceof PlayerQuakeStatistics)
                {

                }
                elseif($game instanceof PlayerHeroBattleStatistics)
                {
                    $item['elo'] = $game->getElo();
                    $item['powerup_taken'] = $game->getPowerupTaken();
                }
                elseif($game instanceof PlayerUpperVoidStatistics)
                {
                    $item['blocks'] = $game->getBlocks();
                    $item['grenades'] = $game->getGrenades();
                    $item['tnt_launched'] = $game->getTNTLaunched();
                }
                elseif($game instanceof PlayerUHCRunStatistics)
                {
                    $item['damages'] = $game->getDamages();
                    $item['max_damages'] = $game->getMaxDamages();
                }
            }
            elseif($game instanceof PlayerJukeboxStatistics)
            {
                $item['mehs'] = $game->getMehs();
                $item['woots'] = $game->getWoots();
            }
        }


        $statistics[$game->getCodeName()] = $item;
    }


    $object['statistics'] = (object) $statistics;


    $shop = array();

    foreach($player->getTransactions() as $transaction)
    {
        $item = array();

        $item['id'] = $transaction->getId();
        $item['date'] = $transaction->getTransactionDate();
        $item['item_id'] = $transaction->getItemReference()->getId();
        $item['item_name'] = utf8_encode($transaction->getItemReference()->getItemName());
        $item['item_description'] = utf8_encode($transaction->getItemReference()->getItemDescription());
        $item['item_minecraft_id'] = $transaction->getItemReference()->getItemMinecraftId();
        $item['item_rarity'] = $transaction->getItemReference()->getItemRarity();
        $item['price_coins'] = $transaction->getItemReference()->getPriceCoins();
        $item['price_stars'] = $transaction->getItemReference()->getPriceStars();
        $item['rank_accessibility'] = $transaction->getItemReference()->getRankAccessibility();
        $item['game_category'] = $transaction->getItemReference()->getGameCategory();
        $item['is_selected'] = $transaction->isSelected();

        array_push($shop, $item);
    }

    $object['transactions'] = $shop;

    $json = json_encode((object) $object, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    echo $json;
}
catch (Exception $ex)
{
    echo json_encode((object) array('success' => false, 'message' => $ex->getMessage()));
}
