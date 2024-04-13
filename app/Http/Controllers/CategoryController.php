<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    public function __construct(){
        $this->middleware(['permission:view_category'])->only(['index','show']);
        $this->middleware(['permission:create_category'])->only(['store']);
        $this->middleware(['permission:update_category'])->only(['update']);
        $this->middleware(['permission:delete_category'])->only(['destroy']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $sort='id';
        $filter_role=null;
        $order='desc';
        $search=null;
        $items = 'paginate';
        $item_perpages = 10;
        if($request->items) {
            $items = $request->items;
        }

        if($request->order){
        $order=    $request->order ;
          $sort=  $request->sort ;
        }
        if($request->search){
            $search= $request->search;
        }
         $query = Category::where('name', 'like', '%' . $search . '%')
           
            ->orderBy($sort, $order);
            if ($request->filter_role) { 
                $query->role($request->filter_role);
            }
             if ($items === 'paginate') {
                $categories = $query->paginate($item_perpages);
            } else if ($items == 'all') {
                $categories = $query->get();
            }
         return CategoryResource::collection($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       if(is_string($request->image)){
        $request->request->remove('image');
       }
        $category =new Category();
        $validator = Validator::make($request->all(), [
            "name"=>["string","required"],
            "description"=>["string","nullable"], 
            "image" => ["nullable","image", "mimes:jpeg,png,jpg,gif,svg", "max:2048"],

        ]);
        if($validator->fails()){
            return response()->json($validator->errors(),500);
        }
        $category->name = $request->name;
        $category->slug=Str::slug($request->name);

        if($request->has("description")&&!is_null($request->description) && !empty($request->description)&& isset($request->description)){
            $category->description = $request->description;
        }
        if ($request->hasFile('image')) {
            $category->image = $request->image->store('categories', 'public');
        }
        $category->save();
        return $category;


    }

    /**
     * Display the specified resource.
     */
    public function show(category $category)
    {
        if(isset($category)){
            return new CategoryResource($category);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, category $category)
    {
        if(is_string($request->image)){
            $request->request->remove('image');
           }
        $validator = Validator::make($request->all(), [
            "name"=>["string","required"],
            "description"=>["string","nullable"] ,
            "image"=>["image","nullable", "mimes:jpeg,png,jpg,gif,svg","max:2048"],
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(),500);
        }
        $category=Category::find($category->id);
        if($category==null){
            return response()->json(["message"=>"category not found"],404);
        }
        if($request->has('name')){
            $category->name = $request->name;
            $category->slug=Str::slug($request->name);

        }
        if($request->has('description')){
            $category->description = $request->description;
        }
       
        if ($request->hasFile('image')) {
            if($category->image){
                Storage::disk('public')->delete($category->image);
            }
            $category->image = $request->image->store('categories', 'public');
        }
        $category->save();
        return $category;
    }

    /**
     * Remove the specified resource from storage.
     */
    
    public function destroy(category $category)
    {
        if(isset($category)){
            $category = Category::find($category->id);
            
            if($category == null){
                return response()->json(["message" => "Category not found"], 404);
            }
            
            if($category->image){
                Storage::disk('public')->delete($category->image);
            }
            
            $category->delete();
            
            return response()->json(["message" => "Category deleted successfully"], 200);
        }
    }

}
