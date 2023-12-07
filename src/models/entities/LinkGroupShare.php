<?php

namespace src\Models\Entities;

use DateTime;
use src\Enums\GroupPermissionLevel;
use src\Helpers\UUIDGenerator;

class LinkGroupShare
{
    public string $link_group_share_id;
    public string $user_id;
    public string $link_group_id;
    public GroupPermissionLevel $permission;
    public DateTime $date_created;

    public function __construct(
        string $user_id,
        string $link_group_id,
        DateTime $date_created = new DateTime(),
        GroupPermissionLevel $permission = GroupPermissionLevel::READ,
        string $link_group_share_id = null
    ) {
        $this->link_group_share_id = $link_group_share_id ?? UUIDGenerator::genUUID();
        $this->user_id = $user_id;
        $this->link_group_id = $link_group_id;
        $this->permission = $permission;
        $this->date_created = $date_created;
    }
}
