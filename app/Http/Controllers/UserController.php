<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Menampilkan daftar user untuk admin
    public function index()
    {
        $users = User::latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    // Menampilkan form tambah user
    public function create()
    {
        return view('admin.users.create');
    }

    // Simpan user baru ke database
    public function store(Request $request)
    {
        // Validasi data input
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'role'     => 'required|in:admin,user',
            'password' => 'required|string|min:6',
        ]);

        // Simpan data user
        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'role'     => $request->role,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('users.index')->with('success', 'Data user berhasil ditambahkan!');
    }

    public function show(User $user)
    {
        //
    }

    // Menampilkan form edit user
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    // Simpan perubahan user ke database
    public function update(Request $request, User $user)
    {
        // Validasi data input
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role'     => 'required|in:admin,user',
            'password' => 'nullable|string|min:6',
        ]);

        $userData = [
            'name'  => $request->name,
            'email' => $request->email,
            'role'  => $request->role,
        ];

        // Jika password diisi baru di-hash dan diubah
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        // Perbarui data user
        $user->update($userData);

        return redirect()->route('users.index')->with('success', 'Data user berhasil diperbarui!');
    }

    // Hapus user
    public function destroy(User $user)
    {
        // Cegah admin menghapus akunnya sendiri yang sedang aktif digunakan
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri!');
        }

        // Hapus data dari database
        $user->delete();

        return redirect()->route('users.index')->with('success', 'Data user berhasil dihapus!');
    }
}
