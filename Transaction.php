<?php


class Transaction {

    private $id;
    private $shop_ref;
    private $price_coins, $price_stars;
    private $transaction_date;
    private $selected;

    /**
     * @param $id int
     * @param $shop_ref ShopRef
     * @param $price_coins int
     * @param $price_stars int
     * @param $transaction_date string
     * @param $selected bool
     */
    public function __construct($id, $shop_ref, $price_coins, $price_stars, $transaction_date, $selected)
    {
        $this->id = $id;
        $this->shop_ref = $shop_ref;
        $this->price_coins = $price_coins;
        $this->price_stars = $price_stars;
        $this->transaction_date = $transaction_date;
        $this->selected = $selected;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return ShopRef
     */
    public function getItemReference()
    {
        return $this->shop_ref;
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
    public function getTransactionDate()
    {
        return $this->transaction_date;
    }

    /**
     * @return boolean
     */
    public function isSelected()
    {
        return $this->selected;
    }
}