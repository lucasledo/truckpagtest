<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            return Product::paginate(10);
        } catch (\Throwable $th) {
            report($th);
            return response()->json([
                'status'  => false,
                'message' => $th->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($code)
    {
        try {
            return Product::where('code', $code)->firstOrFail();
        } catch (\Throwable $th) {
            $message = $th->getMessage();

            if($th instanceof \Illuminate\Database\Eloquent\ModelNotFoundException){
                $message = "Product not found.";
            }

            return response()->json([
                'status'    => false,
                'message'   => $message
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
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
            $message = $th->getMessage();

            if($th instanceof \Illuminate\Database\Eloquent\ModelNotFoundException){
                $message = "Product not found.";
            }

            return response()->json([
                'status'    => false,
                'message'   => $message
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
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
            $message = $th->getMessage();

            if($th instanceof \Illuminate\Database\Eloquent\ModelNotFoundException){
                $message = "Product not found.";
            }

            return response()->json([
                'status'    => false,
                'message'   => $message
            ]);
        }
    }
}
