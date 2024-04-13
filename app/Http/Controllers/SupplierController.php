<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\SupplierResource;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{

    public function __construct(){
        $this->middleware(['permission:view_supplier'])->only(['index','show']);
        $this->middleware(['permission:create_supplier'])->only(['store']);
        $this->middleware(['permission:update_supplier'])->only(['update']);
        $this->middleware(['permission:delete_supplier'])->only(['destroy']);
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
         $query = Supplier::where('last_name', 'like', '%' . $search . '%')
            ->orWhere('first_name', 'like', '%' . $search . '%')
            ->orWhere('email', 'like', '%' . $search . '%')
            ->orWhere('phone', 'like', '%' . $search . '%')
            ->orderBy($sort, $order);
            if ($request->filter_role) { 
                $query->role($request->filter_role);
            }
             if ($items === 'paginate') {
                $suppliers = $query->paginate($item_perpages);
            } else if ($items == 'all') {
                $suppliers = $query->get();
            }
         return SupplierResource::collection($suppliers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       if(is_string($request->image)){
        $request->request->remove('image');
       }
        $supplier =new Supplier();
        $validator = Validator::make($request->all(), [
            "first_name"=>["string","required"],
            "last_name"=>["string","required"] ,
            "email"=>["string","required","email","unique:suppliers,email"],
            "address"=>["string","required"],
            "phone"=>["string","required"],
            "image" => ["nullable","image", "mimes:jpeg,png,jpg,gif,svg", "max:2048"],

        ]);
        if($validator->fails()){
            return response()->json($validator->errors(),500);
        }
        $supplier->first_name = $request->first_name;
        $supplier->last_name = $request->last_name;
        $supplier->email = $request->email;
        $supplier->phone = $request->phone;
        $supplier->address = $request->address;
        if ($request->hasFile('image')) {
            $supplier->image = $request->image->store('suppliers', 'public');
        }
        $supplier->save();
        return $supplier;


    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        if($supplier){
            return new SupplierResource($supplier);
        }
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            "first_name"=>["string","required"],
            "last_name"=>["string","required"] ,
            "email"=>["string","required","email",Rule::unique('suppliers')->ignore($id),
        ],
            "address"=>["string","required"],
            "phone"=>["string","required"],
            "image"=>["image","mimes:jpeg,png,jpg,gif,svg","max:2048"],
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(),500);
        }
        $supplier=Supplier::find($id);
        if($supplier==null){
            return response()->json(["message"=>"supplier not found"],404);
        }
        if($request->has('first_name')){
            $supplier->first_name = $request->first_name;
        }
        if($request->has('last_name')){
            $supplier->last_name = $request->last_name;
        }
        if($request->has('email')){
            $supplier->email = $request->email;
        }
        if($request->has('phone')){
            $supplier->phone = $request->phone;
        }
        if($request->has('address')){
            $supplier->address = $request->address;
        }
        if ($request->hasFile('image')) {
            $supplier->image = $request->image->store('suppliers', 'public');
        }
        $supplier->save();
        return $supplier;
        

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        if($supplier){
            $supplier->delete();
            return response()->json(["message"=>"supplier deleted successfully"],200);
        }
        return response()->json(["message"=>"supplier not found"],404);
    }
}
