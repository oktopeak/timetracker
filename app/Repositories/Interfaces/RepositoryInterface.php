<?php

namespace App\Repositories\Interfaces;

interface RepositoryInterface
{
    public function create($data);
    public function getAll();

    public function update($model, $data);
    public function find($id);

    public function findById($ids);
    public function delete($id);
}
