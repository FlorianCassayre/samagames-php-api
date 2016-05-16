<?php

class ShopRef {

    private $id;
    private $item_name, $item_description, $item_rarity, $item_minecraft_id;
    private $price_coins, $price_stars;
    private $rank_accessibility;
    private $game_category;

    /**
     * @param $id int
     * @param $item_name string
     * @param $item_description string
     * @param $item_rarity string
     * @param $item_minecraft_id string
     * @param $price_coins int
     * @param $price_stars int
     * @param $rank_accessibility string
     * @param $game_category int
     */
    public function __construct($id, $item_name, $item_description, $item_rarity, $item_minecraft_id, $price_coins, $price_stars, $rank_accessibility, $game_category)
    {
        $this->id = $id;
        $this->item_name = $item_name;
        $this->item_description = $item_description;
        $this->item_rarity = $item_rarity;
        $this->item_minecraft_id = $item_minecraft_id;
        $this->price_coins = $price_coins;
        $this->price_stars = $price_stars;
        $this->rank_accessibility = $rank_accessibility;
        $this->game_category = $game_category;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getItemName()
    {
        return $this->item_name;
    }

    /**
     * @return string
     */
    public function getItemDescription()
    {
        return $this->item_description;
    }

    /**
     * @return string
     */
    public function getItemRarity()
    {
        return $this->item_rarity;
    }

    /**
     * @return string
     */
    public function getItemMinecraftId()
    {
        return $this->item_minecraft_id;
    }

    /**
     * @return int
     */
    public function getPriceCoins()
    {
        return $this->price_coins;
    }

    /**
     * @return int
     */
    public function getPriceStars()
    {
        return $this->price_stars;
    }

    /**
     * @return string
     */
    public function getRankAccessibility()
    {
        return $this->rank_accessibility;
    }

    /**
     * @return int
     */
    public function getGameCategory()
    {
        return $this->game_category;
    }
}