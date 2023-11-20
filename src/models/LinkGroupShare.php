<?php

require_once "src/enums/GroupPermissionLevel.php";

class LinkGroupShare
{
    public int $link_group_share_id;
    public int $user_id;
    public int $link_group_id;
    public string $permission;
    public DateTime $date_created;
}