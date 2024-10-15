<?php

namespace App\Repositories\Eloquents;

use Carbon\Carbon;

class DbRepository
{
    /**
     * Eloquent model
     */
    protected $model;

    /**
     * @param $model
     */
    function __construct($model)
    {
        $this->model = $model;
    }

    public function all($columns = ['*'])
    {
        return $this->model->all($columns);
    }

    public function findById($id, $columns = ['*'])
    {
        return $this->model->find($id, $columns);
    }

    public function find($id, $columns = ['*'])
    {
        return $this->model->find($id, $columns);
    }

    public function getByIds($ids)
    {
        return $this->model->whereIn('id', $ids)->get();
    }

    public function create($data)
    {
        return $this->model->create($data);
    }

    public function insert($data)
    {
        foreach ($data as &$item) {
            $item['created_at'] = Carbon::now();
            $item['updated_at'] = Carbon::now();
        }
        return $this->model->insert($data);
    }

    public function destroy($id)
    {
        return $this->model->destroy($id);
    }

    public function count()
    {
        return $this->model->count();
    }

    public function findWhereFirst($condition, $columns = ['*'])
    {
        return $this->model->where($condition)->first($columns);
    }

    public function findWhere($condition, $columns = ['*'])
    {
        return $this->model->where($condition)->select($columns)->get();
    }

    public function updateWhere($condition, $update)
    {
        return $this->model->where($condition)->update($update);
    }

    public function update($data, $id)
    {
        return $this->model->where('id', $id)->update($data);
    }

    public function pluckWhere($condition, $column)
    {
        return $this->model->where($condition)->pluck($column);
    }

    public function pluck($column, $key = null, $sortColumn = null, $direction = 'asc')
    {
        return $this->model->orderBy($sortColumn, $direction)->pluck($column, $key);
    }

    public function orderBy()
    {
        return $this->model->orderBy('created_at', 'desc');
    }

    public function pluckWhereIn($condition, $array, $column)
    {
        return $this->model->whereIn($condition, $array)->pluck($column);
    }

    public function paginate($perPage, $columns = ['*'])
    {
        return $this->model->select($columns)->paginate($perPage);
    }

    public function checkExists($id)
    {
        return $this->model->where('id', $id)->exists();
    }
}
