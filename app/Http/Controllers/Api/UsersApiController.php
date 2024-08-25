<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeUser;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UsersApiController extends Controller
{
    

    public function index()
    {
      
    $users = User::with('roles')->get();

    return response()->json([
        'message' => 'Users fetched successfully',
        'data' => $users,
    ]);
    
    // return response()->json($users);

    }
    public function roles()
    {
        //

        $roles= Role::all();
        // dd($roles);



        return response()->json($roles);


    }


    public function store(Request $request)
{
    // Validate the request data
    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'selectedRole' => 'required|string|exists:roles,name', // Ensure role exists
    ]);

    // Generate a random password
    $password = Str::random(8);
    $validatedData['password'] = Hash::make($password);

    // Create the user
    $user = User::create($validatedData);

    // Assign the selected role to the user
    $role = Role::where('name', $validatedData['selectedRole'])->first();
    if ($role) {
        $user->assignRole($role);
    } else {
        return response()->json(['message' => 'Role not found'], 404);
    }

    // // Dispatch an event or send a welcome email
    // event(new UserCreated($user, $password));

    // Return a response
    return response()->json(['message' => 'User created successfully', 'data' => $user], 201);
}

    // public function store(Request $request)
    // {
    //     //

    //     // dd($request);

    //       //
    //       $validatedData = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|email',
    //         // 'phone' => 'required|string|max:255',
    //         // 'role_id' => 'required|string|max:255',
    //         // 'branch_id' => 'required|string|max:255',
    //         'selectedRole' => 'required|string|max:255',
            
        
    //     ]);
    //     $password = Str::random(8);
    // $validatedData['password'] = Hash::make($password);


    // dd($validatedData);

  
    //     $user = User::create($validatedData);

    //     // dd($validatedData);
        
    //     // dispatch  mail welcome maiil with message 
    //     event(new UserCreated($user, $password));
    //         //   Mail::to($user->email)->send(new WelcomeUser($user->email,$user->name, $password,));
        
    //     return response()->json(['message' => ' created successfully', 'data' => $user], 201);
    
    // }

    public function update(Request $request, string $id)
{
    $user = User::findOrFail($id);

    $validatedData = $request->validate([
        'name' => 'sometimes|required|string|max:255',
        'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
        'password' => 'sometimes|required|string|min:8',
    ]);

    if ($request->has('password')) {
        $validatedData['password'] = bcrypt($validatedData['password']);
    }

    $user->update($validatedData);

    if ($request->has('role')) {
        $roleName = $request->input('role');
        $user->syncRoles([$roleName]);
    }

    return response()->json(['message' => 'User updated successfully', 'data' => $user]);
}


    // public function update(Request $request, string $id)
    // {
    //     //

    //     $user = User::findOrFail($id);

    //     $validatedData = $request->validate([
    //         'name' => 'sometimes|required|string|max:255',
    //         'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
    //         'password' => 'sometimes|required|string|min:8',
    //         // Uncomment these lines if you have additional fields
    //         // 'phone' => 'sometimes|required|string|max:255',
    //         // 'role_id' => 'sometimes|required|string|max:255',
    //         // 'branch_id' => 'sometimes|required|string|max:255',
    //     ]);

    //     if ($request->has('password')) {
    //         $validatedData['password'] = bcrypt($validatedData['password']);
    //     }

    //     $user->update($validatedData);
    //     // $password = Str::random(8);
    


    //     return response()->json(['message' => 'User updated successfully', 'data' => $user]);
    // }

    // public function destroy(string $id)
    // {
    //     //

    //     $user = User::findOrFail($id);
    //     $user->delete();

    //     return response()->json(['message' => 'User deleted successfully']);
    // }


    public function updateUserRole(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        $roleName = $request->input('role');

        // Remove all current roles and assign the new role
        $user->syncRoles([$roleName]);

        return response()->json(['message' => 'Role updated successfully.']);
    }

    public function updatePermissions(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        $permissions = $request->input('permissions', []);

        // Remove all current permissions and assign new ones
        $user->syncPermissions($permissions);

        return response()->json(['message' => 'Permissions updated successfully.']);
    }

    // public function store(Request $request)
    // {
    //     $validatedData = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|string|email|max:255|unique:users',
    //         'phone' => 'required|max:15|unique:users',
    //     ]);

    //     $password = Str::random(8);
    //     $validatedData['password'] = Hash::make($password);

    //     $user = User::create($validatedData);


    //     Mail::to($user->email)->send(new WelcomeUser($user->email,$user->name, $password,));

    //     // dd($validatedData);

    //     return response()->json([
    //         'message' => 'User created successfully',
    //         'user' => $user,
    //         'password' => $password,
    //     ], 201);
    //              // Send welcome email
    //     //  shouldques 
    // }

    // public function update(Request $request, $id)
    // {
    //     $user = User::find($id);

    //     if (!$user) {
    //         return response()->json(['message' => 'User not found'], 404);
    //     }

    //     $validatedData = $request->validate([
    //         'name' => 'sometimes|string|max:255',
    //         'email' => 'string|email',
    //         'phone' => 'sometimes|max:15',
    //         'password' => 'nullable|string|min:8',
    //     ]);

    //     if (!$request->filled('password')) {
    //         unset($validatedData['password']);
    //     } else {
    //         $validatedData['password'] = Hash::make($validatedData['password']);
    //     }

    //     $user->update($validatedData);

    //     return response()->json([
    //         'message' => 'User updated successfully',
    //         'user' => $user,
    //     ]);
    // }

    public function toggleStatus($id)
    {
        $user = User::find($id);

        if (!$user) {
            return Response::json(['message' => 'User not found'], 404);
        }

        // Toggle the status
        $user->status = $user->status ? 0 : 1;
        $user->save();

        return Response::json([
            'message' => 'User status toggled successfully',
            'data' => $user,
        ], 200);
    }
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully'], 204);
    }




    public function permissions()
    {
        //
        $permissions= Permission::all();

        return response()->json($permissions);
    }


// user

public function show($id)
{
    $user = User::findOrFail($id);

    // Get direct permissions
    $directPermissions = $user->permissions;

    // Get permissions via roles
    $rolePermissions = $user->getPermissionsViaRoles();

    // Merge them into one collection
    $allPermissions = $directPermissions->merge($rolePermissions)->pluck('name')->unique();

    return response()->json($allPermissions);
}


// user
    public function updateUserPermissions(Request $request,$id)
    {

        Log::info('User ID received in controller:', ['id' => $id]); // Log the ID received

        $user = User::findOrFail($id);
        $permissions = $request->input('permissions');
        $user->syncPermissions($permissions);
        return response()->json(['message' => 'Permissions updated successfully']);
    }


// Role 
    public function updateRolePermissions(Request $request, $roleId)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'permissions' => 'array', // Expect an array of permission names
            'permissions.*' => 'string|exists:permissions,name', // Each permission must be a string and exist in the database
        ]);

        // Find the role by ID
        $role = Role::findById($roleId);

        if (!$role) {
            return response()->json(['error' => 'Role not found'], 404);
        }

        // Sync the permissions
        $role->syncPermissions($validated['permissions']);

        return response()->json([
            'message' => 'Permissions updated successfully',
            'role' => $role->name,
            'permissions' => $role->permissions()->pluck('name'), // Return the updated permissions
        ]);
    }
  

  
    /**
     * Get permissions for a specific role.
     */
    public function getRolePermissions($roleId)
    {
        $role = Role::findOrFail($roleId);
        $permissions = $role->permissions;

        return response()->json(['permissions' => $permissions->pluck('name')]);
    }



    /**
     * Create a new role.
     */
    public function storeRole(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $role = Role::create(['name' => $validated['name']]);
        $role->syncPermissions($validated['permissions'] ?? []);

        return response()->json([
            'message' => 'Role created successfully',
            'role' => $role->name,
            'permissions' => $role->permissions()->pluck('name'),
        ]);
    }

    /**
     * Update an existing role.
     */
    public function updateRole(Request $request, $roleId)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name,' . $roleId,
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $role = Role::findOrFail($roleId);
        $role->update(['name' => $validated['name']]);
        $role->syncPermissions($validated['permissions'] ?? []);

        return response()->json([
            'message' => 'Role updated successfully',
            'role' => $role->name,
            'permissions' => $role->permissions()->pluck('name'),
        ]);
    }

    /**
     * Create a new permission.
     */
    public function storePermission(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:permissions,name',
        ]);

        $permission = Permission::create(['name' => $validated['name']]);

        return response()->json([
            'message' => 'Permission created successfully',
            'permission' => $permission->name,
        ]);
    }

    /**
     * Update an existing permission.
     */
    public function updatePermission(Request $request, $permissionId)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:permissions,name,' . $permissionId,
        ]);

        $permission = Permission::findOrFail($permissionId);
        $permission->update(['name' => $validated['name']]);

        return response()->json([
            'message' => 'Permission updated successfully',
            'permission' => $permission->name,
        ]);
    }



    /**
 * Remove the specified role from storage.
 */
public function destroyRole($roleId)
{
    // Find the role by ID
    $role = Role::findById($roleId);

    if (!$role) {
        return response()->json(['error' => 'Role not found'], 404);
    }

    // Delete the role
    $role->delete();

    return response()->json(['message' => 'Role deleted successfully']);
}


/**
 * Remove the specified permission from storage.
 */
public function destroyPermission($permissionId)
{
    // Find the permission by ID
    $permission = Permission::findById($permissionId);

    if (!$permission) {
        return response()->json(['error' => 'Permission not found'], 404);
    }

    // Delete the permission
    $permission->delete();

    return response()->json(['message' => 'Permission deleted successfully']);
}

}