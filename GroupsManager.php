<?php

require_once('Group.php');

class GroupsManager
{
    private $db;

    private $groups = array();

    /**
     * @param $db PDO
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * @param $id int
     * @return object
     */
    public function getGroup($id)
    {
        if(isset($this->groups[$id]))
            return $this->groups[$id];

        $stm = $this->db->prepare("SELECT * FROM groups WHERE group_id = :id");
        $stm->bindParam(':id', $id);
        $stm->execute();
        $fetch = $stm->fetchAll();

        if(count($fetch) == 0)
            return null;

        $group = $this->createGroup($fetch[0]);
        $this->groups[$id] = $group;

        return $group;
    }

    public function loadAllGroups()
    {
        $stm = $this->db->prepare("SELECT * FROM groups");
        $stm->execute();
        $fetch = $stm->fetchAll();

        foreach($fetch as $row)
        {
            $group = $this->createGroup($row);
            $this->groups[$group->getId()] = $group;
        }
    }

    /**
     * @param $row
     * @return Group
     */
    private function createGroup($row)
    {
        $group = new Group(intval($row['group_id']), $row['group_name'], $row['tag'], intval($row['rank']), $row['prefix'], $row['suffix'], intval($row['multiplier']));

        return $group;
    }
}