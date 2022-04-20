<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Product\ProductRepositoryInterface;
use App\Repositories\Category\CategoryRepositoryInterface;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Exception;

class ProductController extends Controller
{
    protected $productRepository;
    protected $categoryRepository;
    public function __construct(ProductRepositoryInterface $productRepository, CategoryRepositoryInterface $categoryRepository)
    {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function index(Request $request)
    {
        $products = $this->productRepository->all(['*'],['categories']);
        if ($products->isEmpty()) {
            return response([__('message')=> __('NOT FOUND')], Response::HTTP_NOT_FOUND);
        }
        return response([__('message')=> __('success'),__('products')=> $products], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        try {
            $input = $request->all();
            $validator = Validator::make($input, $this->productRepository->rules($id=null));
            if ($validator->fails()) {
                return response([__('message')=> __('Errors'), 'Errors'=> $validator->errors()], Response::HTTP_BAD_REQUEST);
            }
            $product = $this->productRepository->create($input);
            $categoryId = $input['categories'] ?? null;
            $category = isset($categoryId) ? $this->categoryRepository->findById($categoryId) : null;
            if ($category) {
                $product->categories()->attach($categoryId);
            }
            return response([__('message')=> __('created'),'products'=> $product], Response::HTTP_CREATED);
        } catch (\Exception $e){
            $message = $e->getMessage();
            if ('production' == env('APP_ENV')) {
                $message = __('Không thể tạo product');
            }
            return response([__('message')=> __('Errors'), ['errors' => $message]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
    public function update(Request $request, $id) 
    {
        try {
            $input = $request->all();
            $validator = Validator::make($input, $this->productRepository->rules($id));
            if ($validator->fails()) {
                return response([__('message')=> __('Errors'), 'Errors'=> $validator->errors()], Response::HTTP_BAD_REQUEST);
            }
            $product = $this->productRepository->findById($id) ?? null;
            if(empty($product)) {
                return response([__('message')=> __('Not found')], Response::HTTP_NOT_FOUND);
            }
            $categoryId = $input['categories'] ?? null;
            $category = isset($categoryId) ? $this->categoryRepository->findById($categoryId) : null;
            if ($category) {
                $product->categories()->sync($categoryId);
            }
            $product->update($input);
            return response([__('message')=> __('Update success'),'products'=> $product], Response::HTTP_OK);
        } catch(Exception $e) 
        {
            return response([__('message')=> __('Errors'), $e->getMessage() ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function destroy($id) 
    {
        try {
            $product = $this->productRepository->findById($id);
            if (!$product) {
                return response([__('message')=> __('Not found')], Response::HTTP_NOT_FOUND);
            }
            $countRelationshipProduct = $product->categories()->count();
            if($countRelationshipProduct) {
                $product->categories()->delete();
            }
            $this->productRepository->deleteById($id);
            return response([], Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            if ('production' == env('APP_ENV')) {
                $message = __('Không thể xóa Product');
            }
            return response([__('message')=> __('Errors'), ['errors' => $message]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
