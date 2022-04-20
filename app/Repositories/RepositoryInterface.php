<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface  RepositoryInterface {

    /**
     * @param array $rules
     * @return $rules
     */
    public function rules(int $id): array;
    
    /**
     * Get all models.
     *
     * @param array $columns
     * @param array $relations
     * @return LengthAwarePaginator
     */
    public function all(array $columns = ['*'], array $relations = []): ?LengthAwarePaginator;

     /**
     * Find model by id.
     *
     * @param int $modelId
     * @return Model
     */
    public function findById(int $modelId): ?Model;

    /**
     * Create a model.
     *
     * @param array $attributes
     * @return Model
     */
    public function create(array $attributes): ?Model;

     /**
     * Update existing model.
     *
     * @param int $modelId
     * @param array $attributes
     * @return bool
     */
    public function update(int $modelId, array $attributes): bool;

    
    /**
     * Delete model by id.
     *
     * @param int $modelId
     * @return bool
     */
    public function deleteById(int $modelId): bool;


}
