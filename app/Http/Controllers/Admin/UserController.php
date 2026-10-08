<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of user accounts.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Search filter
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Role filter
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        // Statistics
        $totalUsers = User::count();
        $adminUsers = User::where('role', 'admin')->count();
        $clientUsers = User::where('role', 'user')->count();
        $verifiedUsers = User::whereNotNull('email_verified_at')->count();

        return view('admin.users.index', compact(
            'users', 
            'totalUsers', 
            'adminUsers', 
            'clientUsers', 
            'verifiedUsers'
        ));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:admin,user',
            'phone'    => 'nullable|string|max:20',
        ], [
            'name.required'     => 'Vui lòng nhập họ tên người dùng.',
            'email.required'    => 'Vui lòng nhập địa chỉ email.',
            'email.unique'      => 'Địa chỉ email này đã tồn tại trên hệ thống.',
            'password.required' => 'Vui lòng nhập mật khẩu khởi tạo.',
            'password.min'      => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'role.required'     => 'Vui lòng chọn vai trò cho tài khoản.',
        ]);

        $username = explode('@', $request->email)[0] . '_' . rand(100, 999);

        User::create([
            'name'              => $request->name,
            'username'          => $username,
            'email'             => $request->email,
            'password'          => Hash::make($request->password),
            'role'              => $request->role,
            'phone'             => $request->phone,
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Thêm mới tài khoản người dùng thành công.');
    }

    /**
     * Display the specified user detail.
     */
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'role'     => 'required|in:admin,user',
            'phone'    => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6',
        ], [
            'name.required'  => 'Vui lòng nhập họ tên người dùng.',
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.unique'   => 'Địa chỉ email này đã được sử dụng.',
            'role.required'  => 'Vui lòng chọn vai trò.',
            'password.min'   => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
        ]);

        // Security Guard: Admin cannot demote their own account role
        if ($user->id === Auth::id() && $request->role !== 'admin') {
            return redirect()->back()->with('error', 'Bạn không thể tự hạ cấp (thay đổi vai trò) tài khoản Admin của chính mình!');
        }

        $updateData = [
            'name'  => $request->name,
            'email' => $request->email,
            'role'  => $request->role,
            'phone' => $request->phone,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return redirect()->route('admin.users.index')
            ->with('success', 'Cập nhật thông tin tài khoản thành công.');
    }

    /**
     * Remove the specified user account.
     */
    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'Bạn không thể xóa tài khoản của chính mình!');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Đã xóa tài khoản ' . ($user->username ?? $user->name) . ' thành công.');
    }
}
