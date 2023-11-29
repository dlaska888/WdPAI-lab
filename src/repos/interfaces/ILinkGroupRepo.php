<?php

require_once "src/models/LinkGroup.php";

interface ILinkGroupRepo
{
    public function all(): array;

    public function findById(string $linkGroupId): ?LinkGroup;

    public function insert(LinkGroup $linkGroup): LinkGroup;

    public function update(LinkGroup $linkGroup): LinkGroup;

    public function delete(string $linkGroupId): bool;
}
