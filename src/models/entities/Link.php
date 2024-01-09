<?php

namespace src\Models\Entities;

use src\Helpers\UUIDGenerator;
use DateTime;

class Link
{
    public string $link_id;
    public string $link_group_id;
    public string $title;
    public string $url;
    public DateTime $date_created;

    public function __construct(
        string $link_group_id,
        string $title,
        string $url,
        DateTime $date_created = new DateTime(),
        string $link_id = null,
    ) {
        $this->link_id = $link_id ?? UUIDGenerator::genUUID();
        $this->link_group_id = $link_group_id;
        $this->title = $title;
        $this->url = $url;
        $this->date_created = $date_created;
    }
}
