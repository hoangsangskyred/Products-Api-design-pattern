<?php

namespace App\Repositories\Product;
use App\Repositories\BaseRepository;
use App\Models\Product;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface 
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
    public function __construct(Product $model)
    {
        $this->model = $model;
    }

    public function rules($id): array
    {
        $rules = [
            'name' =>'required|max:255|unique:products,name,'.$id,
            'slug' => 'required|max:255|unique:products,name,'.$id,
            'color' =>'nullable',
            'quantity'=>'nullable',
            'is_active'=>'nullable',
        ];
        return $rules;
    }
}