<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::withCount('users')->get();
        return view('roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('roles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles',
            'description' => 'nullable|string|max:255',
        ]);

        Role::create($request->all());

        return redirect()->route('roles.index')
            ->with('success', 'Vai trò đã được tạo thành công.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $role = Role::with('users')->findOrFail($id);
        return view('roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $role = Role::findOrFail($id);
        return view('roles.edit', compact('role'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $id,
            'description' => 'nullable|string|max:255',
        ]);

        $role = Role::findOrFail($id);
        $role->update($request->all());

        return redirect()->route('roles.index')
            ->with('success', 'Vai trò đã được cập nhật thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::findOrFail($id);
        
        // Kiểm tra xem có user nào đang sử dụng vai trò này không
        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')
                ->with('error', 'Không thể xóa vai trò này vì đang có người dùng sử dụng.');
        }
        
        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Vai trò đã được xóa thành công.');
    }
    
    /**
     * Display users with the specified role.
     */
    public function users(string $id)
    {
        $role = Role::findOrFail($id);
        $users = User::where('role_id', $id)->paginate(15);
        
        return view('roles.users', compact('role', 'users'));
    }
    
    /**
     * Show the form for assigning roles to users.
     */
    public function assignForm()
    {
        $users = User::all();
        $roles = Role::all();
        
        return view('roles.assign', compact('users', 'roles'));
    }
    
    /**
     * Assign role to user.
     */
    public function assignRole(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'nullable|exists:roles,id',
        ]);
        
        $user = User::findOrFail($request->user_id);
        
        // Nếu role_id là null hoặc chuỗi rỗng, xóa vai trò của người dùng
        if ($request->role_id === null || $request->role_id === '') {
            $user->update(['role_id' => null]);
            $message = 'Đã xóa vai trò của người dùng.';
        } else {
            $role = Role::findOrFail($request->role_id);
            $user->update(['role_id' => $role->id]);
            $message = 'Đã gán vai trò ' . $role->name . ' cho người dùng.';
        }
        
        return redirect()->back()->with('success', $message);
    }
}
