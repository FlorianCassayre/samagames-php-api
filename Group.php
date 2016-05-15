<?php

class Group
{
    private $id, $name, $tag, $rank;
    private $prefix, $suffix;
    private $multiplier;

    /**
     * @param $id int
     * @param $name string
     * @param $tag string
     * @param $rank int
     * @param $prefix string
     * @param $suffix string
     * @param $multiplier int
     */
    public function __construct($id, $name, $tag, $rank, $prefix, $suffix, $multiplier)
    {
        $this->id = $id;
        $this->name = $name;
        $this->tag = $tag;
        $this->rank = $rank;
        $this->prefix = $prefix;
        $this->suffix = $suffix;
        $this->multiplier = $multiplier;
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getTagRaw()
    {
        return $this->tag;
    }

    /**
     * @return string
     */
    public function getTag()
    {
        $replace = '0123456789abcdefklmnors';
        $clean_tag = $this->tag;

        for ($i = 0; $i < strlen($replace); $i++)
            $clean_tag = str_replace('&' . $replace[$i], '', $clean_tag);

        return $clean_tag;
    }

    /**
     * @return int
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @return string
     */
    public function getSuffix()
    {
        return $this->suffix;
    }

    /**
     * @return int
     */
    public function getMultiplier()
    {
        return $this->multiplier;
    }

    /**
     * @return bool
     */
    public function isStaff()
    {
        return $this->id >= 6;
    }
}