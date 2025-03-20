<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * List all products (paginated).
     *
     * @group Products
     * @authenticated
     * @response 200 {"data": [], "links": {}, "meta": {}}
     * @response 500 {"status": false, "message": "Error"}
     */
    public function index()
    {
        try {
            return Product::paginate(10);
        } catch (\Throwable $th) {
            report($th);
            return response(json_encode([
                'status'  => false,
                'message' => $th->getMessage()
            ]), 500);
        }
    }

    /**
     * Get a specific product by code.
     *
     * @group Products
     * @authenticated
     * @urlParam code string required The product code. Example: 12345
     * @response 200 {"code": 12345, "product_name": "Example Product"}
     * @response 404 {"status": false, "message": "Product not found."}
     * @response 500 {"status": false, "message": "Error"}
     */
    public function show($code)
    {
        try {
            return Product::where('code', $code)->firstOrFail();
        } catch (\Throwable $th) {

            if($th instanceof \Illuminate\Database\Eloquent\ModelNotFoundException){
                return response(json_encode([
                    'status'    => false,
                    'message'   => "Product not found."
                ]), 404);
            }

            report($th);
            return response(json_encode([
                'status'    => false,
                'message'   => $th->getMessage()
            ]), 500);
        }
    }

    /**
     * Update a product by code.
     *
     * @group Products
     * @authenticated
     * @bodyParam code string required The code of the product. Example: "Example code"
     * @bodyParam status string required The status of the product. Example: "Example status"
     * @bodyParam imported_t string required The imported_t of the product. Example: "Example imported_t"
     * @bodyParam url string required The url of the product. Example: "Example url"
     * @bodyParam creator string required The creator of the product. Example: "Example creator"
     * @bodyParam created_t string required The created_t of the product. Example: "Example created_t"
     * @bodyParam last_modified_t string required The last_modified_t of the product. Example: "Example last_modified_t"
     * @bodyParam product_name string required The product_name of the product. Example: "Example product_name"
     * @bodyParam quantity string required The quantity of the product. Example: "Example quantity"
     * @bodyParam brands string required The brands of the product. Example: "Example brands"
     * @bodyParam categories string required The categories of the product. Example: "Example categories"
     * @bodyParam labels string required The labels of the product. Example: "Example labels"
     * @bodyParam cities string required The cities of the product. Example: "Example cities"
     * @bodyParam purchase_places string required The purchase_places of the product. Example: "Example purchase_places"
     * @bodyParam stores string required The stores of the product. Example: "Example stores"
     * @bodyParam ingredients_text string required The ingredients_text of the product. Example: "Example ingredients_text"
     * @bodyParam traces string required The traces of the product. Example: "Example traces"
     * @bodyParam serving_size string required The serving_size of the product. Example: "Example serving_size"
     * @bodyParam serving_quantity string required The serving_quantity of the product. Example: "Example serving_quantity"
     * @bodyParam nutriscore_score string required The nutriscore_score of the product. Example: "Example nutriscore_score"
     * @bodyParam nutriscore_grade string required The nutriscore_grade of the product. Example: "Example nutriscore_grade"
     * @bodyParam main_category string required The main_category of the product. Example: "Example main_category"
     * @bodyParam image_url string required The image_url of the product. Example: "Example image_url"
     * @response 200 {"status": true, "message": "Product has been updated."}
     * @response 404 {"status": false, "message": "Product not found."}
     * @response 500 {"status": false, "message": "Error"}
     */
    public function update($code, Request $request)
    {
        try {
            $product = Product::where('code', $code)->firstOrFail();

            $product->fill($request->all());
            $product->save();

            return response()->json([
                'status'    => true,
                'message'   => "Product has been updated."
            ]);

        } catch (\Throwable $th) {

            if($th instanceof \Illuminate\Database\Eloquent\ModelNotFoundException){
                return response(json_encode([
                    'status'    => false,
                    'message'   => "Product not found."
                ]), 404);
            }

            report($th);
            return response(json_encode([
                'status'    => false,
                'message'   => $th->getMessage()
            ]), 500);
        }
    }

    /**
     * Soft delete a product by changing its status to "trash".
     *
     * @group Products
     * @authenticated
     * @urlParam code string required The product code. Example: 12345
     * @response 200 {"status": true, "message": "Product has been changed the status for 'trash'"}
     * @response 404 {"status": false, "message": "Product not found."}
     * @response 500 {"status": false, "message": "Error"}
     */
    public function destroy($code)
    {
        try {
            $product = Product::where('code', $code)->firstOrFail();

            $product->status = 'trash';
            $product->save();

            return response()->json([
                'status'    => true,
                'message'   => "Product has been changed the status for 'trash'"
            ]);

        } catch (\Throwable $th) {
            if($th instanceof \Illuminate\Database\Eloquent\ModelNotFoundException){
                return response(json_encode([
                    'status'    => false,
                    'message'   => "Product not found."
                ]), 404);
            }

            report($th);
            return response(json_encode([
                'status'    => false,
                'message'   => $th->getMessage()
            ]), 500);
        }
    }
}
