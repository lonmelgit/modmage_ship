<?php

namespace ModMage\Ship\Api;

interface WaysRepositoryInterface
{
    public function save(\ModMage\Ship\Api\Data\WaysInterface $ways);

    public function getById($id);

    public function delete(\ModMage\Ship\Api\Data\WaysInterface $ways);

    public function deleteById($id);
}
