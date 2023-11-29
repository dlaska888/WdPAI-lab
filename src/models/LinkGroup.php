<?php

require_once "src/helpers/UUIDGenerator.php";

class LinkGroup
{
    public string $link_group_id;
    public string $user_id;
    public string $name;
    public DateTime $date_created;

    public function __construct(
        string $user_id,
        string $name,
        DateTime $date_created,
        string $link_group_id = null
    ) {
        $this->link_group_id = $link_group_id ?? UUIDGenerator::genUUID();
        $this->user_id = $user_id;
        $this->name = $name;
        $this->date_created = $date_created;
    }
}
