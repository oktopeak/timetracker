<?php

namespace App\Repositories\Implementations;

use Illuminate\Database\Eloquent\Model;

class Repository
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function findById($id)
    {
        return $this->model->find($id);
    }

    public function findWith($id, $with)
    {
        return $this->model->with($with)->find($id);
    }

    public function create($data)
    {
        return $this->model->create($data);
    }

    public function update($model, $data)
    {
        $model->update($data);
        return $model;
    }

    public function save($model)
    {
        return $model->save();
    }

    public function destroy($model)
    {
        return $model->delete();
    }
}
