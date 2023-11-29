<?php

require_once "src/models/Link.php";

interface ILinkRepo
{
    public function all(): array;

    public function findById(string $linkId): ?Link;

    public function insert(Link $link): Link;

    public function update(Link $link): Link;

    public function delete(string $linkId): bool;
}
