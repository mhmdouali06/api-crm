<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function __construct(){
        $this->middleware('permission:view_roles|view_user', ['only' => ['index','show']]);
        $this->middleware('permission:create_roles', ['only' => ['store']]);
        $this->middleware('permission:update_roles', ['only' => ['update']]);
        $this->middleware('permission:delete_roles', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sort='id';
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
         $query = Role::with('permissions')
         ->where('name', 'like', '%' . $search . '%')
             ->orderBy($sort, $order);
     
             if ($items === 'paginate') {
                $roles = $query->paginate($item_perpages);
            } else if ($items == 'all') {
                $roles = $query->get();
            }
         return $roles;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            "permissions" => "required|array",
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 500);
        }
        $role = Role::create(['name' => $request->name]);
        if($request->permissions){
            foreach($request->permissions as $permission){
                $role->givePermissionTo($permission);
            }
        }
        $role->syncPermissions($request->permissions);
        return response()->json($role);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id )
    {
        $role = Role::with('permissions')->find($id);
        if($role){
            return response()->json($role);
        }
        
    }

    /**
     * Update the specified resource in storage.
     */
   /**
 * Update the specified resource in storage.
 */
public function update(Request $request, string $id)
{
   
    $role = Role::find($id);

    if (!$role) {
        return response()->json(['error' => 'Role not found'], 404);
    }

    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'permissions' => 'required|array',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    $role->update(['name' => $request->name]);
    $role->syncPermissions($request->permissions);

    return response()->json($role);
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::find($id);
        if($role){
            $role->delete();
            return response()->json(['message' => 'Role deleted successfully']);
        }
    }
}
