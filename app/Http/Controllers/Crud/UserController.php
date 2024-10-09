<?php

namespace App\Http\Controllers\Crud;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\RouteDiscovery\Attributes\Route;

class UserController extends Controller
{
    public function index()
    {
        return User::all(); // Menampilkan semua pengguna
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        return User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
    }

    #[Route('GET', '{id}')]
    public function show($id)
    {
        return User::findOrFail($id); // Menampilkan pengguna berdasarkan ID
    }

    #[Route('POST|PUT', '{id}')]
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update($request->all());
        return $user;
    }

    #[Route('DELETE', '{id}')]
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'User deleted']);
    }
}
