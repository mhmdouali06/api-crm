<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;




class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(){
        $this->middleware(['permission:view_user'])->only(['index','show']);
        $this->middleware(['permission:create_user'])->only(['store']);
        $this->middleware(['permission:update_user'])->only(['update']);
        $this->middleware(['permission:delete_user'])->only(['destroy']);
    }
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
         $query = User::with('roles')
            ->where('last_name', 'like', '%' . $search . '%')
            ->orWhere('first_name', 'like', '%' . $search . '%')
            ->orWhere('email', 'like', '%' . $search . '%')
            ->orWhere('phone', 'like', '%' . $search . '%')
            ->orderBy($sort, $order);
            if ($request->filter_role) { 
                $query->role($request->filter_role);
            }
             if ($items === 'paginate') {
                $users = $query->paginate($item_perpages);
            } else if ($items == 'all') {
                $users = $query->get();
            }
         return UserResource::collection($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user =new User();
        $validator = Validator::make($request->all(), [
            "first_name"=>["string","required"],
            "last_name"=>["string","required"] ,
            "email"=>["string","required","email","unique:users,email"],
            "address"=>["string","required"],
            "password"=>["string","required"],
            "role"=>["string","required"],
            "phone"=>["string","required"],
            "image"=>["image","mimes:jpeg,png,jpg,gif,svg","max:2048"],

        ]);
        if($validator->fails()){
            return response()->json($validator->errors(),500);
        }
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->password = Hash::make($request->password);
        if ($request->hasFile('image')) {
            $user->image = $request->image->store('users', 'public');
        }
        $user->assignRole($request->role);
        $user->save();
        return $user;


    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
         $user=User::with('roles')->find($id);
         if($user==null){
            return response()->json(["message"=>"user not found"],404);
        }
         return new UserResource($user);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            "first_name" => ["string", "nullable"],
            "last_name" => ["string", "nullable"],
            // "email" => ["string", "nullable", "email", "unique:users,email"],
            "address" => ["string", "nullable"],
            "password" => ["string", "nullable","confirmed"],
            "role" => ["string", "nullable"],
            "phone" => ["string", "nullable"],
            // "image" => ["nullable", "string", "image", "mimes:jpeg,png,jpg,gif,svg", "max:2048"],
        ]);
        
        if($validator->fails()){
            return response()->json($validator->errors(),500);
        }
        $user=User::find($id);
        if($user==null){
            return response()->json(["message"=>"user not found"],404);
        }
        if($request->has('first_name')){
            $user->first_name = $request->first_name;
        }
        if($request->has('last_name')){
            $user->last_name = $request->last_name;
        }
        // if($request->has('email')){
        //     $user->email = $request->email;
        // }
        if($request->has('phone')){
            $user->phone = $request->phone;
        }
        if($request->has('address')){
            $user->address = $request->address;
        }
        if($request->has('password')){
            $user->password = Hash::make($request->password);
        }
        if ($request->hasFile('image')) {
            $user->image = $request->image->store('users', 'public');
        }
        if($request->has('role')){
            $user->assignRole($request->role);
        }
        $user->save();
        return $user;

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user=User::find($id);
        if($user==null){
            return response()->json(["message"=>"user not found"],404);
        }
        $user->delete();
        return response()->json(["message"=>"user deleted successfully"],200);
    }
}
