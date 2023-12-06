<?php

namespace src\Models;

use src\Helpers\UUIDGenerator;
use Throwable;

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

    public static function fromArray(array $data): self | null
    {
        try{
            return new self(
                $data['link_group_id'],
                $data['title'],
                $data['url'],
                $data['link_id'] ?? null
            );   
        } catch (Throwable){
            return null;
        }
        
    }

    public function toArray(): array
    {
        return [
            'link_id' => $this->link_id,
            'link_group_id' => $this->link_group_id,
            'title' => $this->title,
            'url' => $this->url,
        ];
    }
}
