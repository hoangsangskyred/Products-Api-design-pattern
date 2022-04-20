<?php

namespace App\Repositories\Category;

use App\Repositories\BaseRepository;
use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface 
{

    /**
     * @var Model
     */
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(Category $model)
    {
        $this->model = $model;
    }

    public function rules($id): array
    {
        $rules = [
            'name' =>'required|max:255|unique:categories,name,'.$id,
            'slug' => 'required|max:255|unique:categories,name,'.$id,
            'parent' =>'nullable',
            'is_active'=>'nullable',
        ];
        return $rules;
    }

    public function all(array $columns = ['*'], array $relations = []): ?LengthAwarePaginator
    {
        return $this->model->whereNull('parent')->with($relations)->select($columns)->paginate(10);
    }
    
}