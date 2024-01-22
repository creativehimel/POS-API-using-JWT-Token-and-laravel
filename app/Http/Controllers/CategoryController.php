<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function getCategoryList(Request $request){
        $userId = $request->header("user_id");
        $categories = Category::where("user_id",$userId)->get();
        return $categories;
    }
    public function createCategory(Request $request)
    {
        try {
            $request->validate([
                "name"=> "required|string",
                "description"=> "required|string",
                "status"=> "required|boolean",
                ]);
                $request['user_id'] = $request->header("user_id");
                $request['slug'] = Str::slug($request->name);
                $category = Category::create($request->all());
                return response()->json([
                    'status'=> 'success',
                    'message' => 'Category created successfully.'
                ],200);

        } catch (Exception $e) {
            return response()->json([
                'status'=> 'error',
                'message'=> $e->getMessage()
                ],500);
        }

    }
}
