<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BaseRepository  implements  RepositoryInterface
{
    /**
     * @param array $rules
     * 
    */
    protected $rules;

    /**
     * @var Model
      */
    protected $model;


    public  function  __construct(Model $model)
    {
        $this->model = $model;
    }
    /**
     * @param $rules
     * @return array $rulus
     */
    public function rules(int $id): array
    {
        return $this->rules;
    }
    
    /**
     * @param array $columns
     * @param array $relations
     * @return LengthAwarePaginator
     */
    public function all(array $columns = ['*'], array $relations = []): ?LengthAwarePaginator
    {
        return $this->model->with($relations)->select($columns)->paginate(10);
    }
     /**
     * Find model by id.
     *
     * @param int $modelId
     * @return Model
     */
    public function findById(int $modelId): ?Model 
    {
        return $this->model->find($modelId);
    }
       /**
     * Create a model.
     *
     * @param array $attributes
     * @return Model
     */
    public function create(array $attributes): ?Model
    {
        $model = $this->model->create($attributes);
        return $model;
    }

     /**
     * Update existing model.
     *
     * @param int $modelId
     * @param array $attributes
     * @return bool
     */
    public function update(int $modelId, array $attributes): bool
    {
        $model = $this->findById($modelId);

        return $model->update($attributes);
    }

     /**
     * Delete model by id.
     *
     * @param int $modelId
     * @return bool
     */
    public function deleteById(int $modelId): bool
    {
        return $this->findById($modelId)->delete();
    }
}
