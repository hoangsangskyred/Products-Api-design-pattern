<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Category\CategoryRepositoryInterface;
use App\Repositories\Product\ProductRepositoryInterface;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Exception;

class CategoryController extends Controller
{
    protected $categoryRepository;

    protected $productRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository, ProductRepositoryInterface $productRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->productRepository =  $productRepository;
    }

    public function index(Request $request)
    {
        $category = $this->categoryRepository->all(['*'],['products', 'childrenCategory']);
        if ($category->isEmpty()) {
            return response([__('message')=> __('NOT FOUND')], Response::HTTP_NOT_FOUND);
        }
        return response([__('message')=> __('success'),'category'=> $category], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        try {
            $input = $request->all();
            $validator = Validator::make($input, $this->categoryRepository->rules($id=null));
            if ($validator->fails()) {
                return response([__('message')=> __('Errors'), 'Errors'=> $validator->errors()], Response::HTTP_BAD_REQUEST);
            }
            $category =$this->categoryRepository->create($input);
            $productId = $input['products'] ?? null;
            $product = isset($productId) ? $this->productRepository->findById($productId) : null;
            if ($product) {
                $category->products()->attach($productId);
            }
            return response([__('message')=> __('created'),'category'=> $category], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            if ('production' == env('APP_ENV')) {
                $message = __('Không thể tạo category');
            }
            return response([__('message')=> __('Errors'), ['errors' => $message]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $input = $request->all();
            $validator = Validator::make($input, $this->categoryRepository->rules($id));
            if ($validator->fails()) {
                return response([__('message')=> __('Errors'), 'Errors'=> $validator->errors()], Response::HTTP_BAD_REQUEST);
            }
            $category = $this->categoryRepository->findById($id) ?? null;
            if (empty($category)) {
                return response([__('message')=> __('Not found')], Response::HTTP_NOT_FOUND);
            }
            $productId = $input['products'] ?? null;
            $product = isset($productId) ? $this->productRepository->findById($productId) : null;
            if($product) 
            {
                $category->products()->sync($productId);
            }
            $category->update($input);
            return response([__('message')=> __('Update success'), 'category'=> $category], Response::HTTP_OK);
        } catch (\Exception $e) 
        {   
            $message = $e->getMessage();
            if ('production' == env('APP_ENV')) {
                $message = __('Không thể xóa Update Category');
            }
            return response([__('message')=> __('Errors'), $e->getMessage() ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function destroy($id) 
    {
        try {
            $category = $this->categoryRepository->findById($id);
            if (!$category) {
                return response([__('message')=> __('Not found')], Response::HTTP_NOT_FOUND);
            }
            $countRelationshipChildren =  $category->childrenCategory()->count();
            if ($countRelationshipChildren > 0) {
                $category->childrenCategory()->delete();
            }
            $countRelationshipProduct = $category->products()->count();
            if ($countRelationshipProduct) {
                $category->products()->delete();
            }
            $this->categoryRepository->deleteById($id);
            return response([], Response::HTTP_NO_CONTENT);     
        } catch (\Exception $e) {
            $message = $e->getMessage();
            if ('production' == env('APP_ENV')) {
                $message = __('Không thể xóa Category');
            }
            return response([__('message')=> __('Errors'), ['errors' => $message]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

}
