<?php

require_once "src/enums/GroupPermissionLevel.php";

class LinkGroupShare
{
    public int $linkGroupShareId;
    public int $userId;
    public int $linkGroupId;
    public GroupPermissionLevel $permission;
    public DateTime $dateCreated;
}