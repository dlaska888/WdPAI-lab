<?php

namespace src\Models\Entities;

use src\Helpers\UUIDGenerator;

class Link
{
    public string $link_id;
    public string $link_group_id;
    public string $title;
    public string $url;

    public function __construct(
        string $link_group_id,
        string $title,
        string $url,
        string $link_id = null
    ) {
        $this->link_id = $link_id ?? UUIDGenerator::genUUID();
        $this->link_group_id = $link_group_id;
        $this->title = $title;
        $this->url = $url;
    }

}
