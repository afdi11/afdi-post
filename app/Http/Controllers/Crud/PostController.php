<?php

namespace App\Http\Controllers\Crud;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Spatie\RouteDiscovery\Attributes\Route;

class PostController extends Controller
{
    public function index()
    {
        $post = Cache::remember('bookmark', 3600, function() {
            return Post::with('user', 'postCategory')->get();
        });
        return $post; // Menampilkan semua post dengan relasi
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'user_id' => 'required|exists:users,id', // Validasi agar user_id harus ada di tabel users
            'post_category_id' => 'required|exists:post_categories,id',
        ]);

        $post = Post::create($request->all());

        if ($request->hasFile('image')) {
            $post->addMedia($request->file('image'))->toMediaCollection('images');
        }

        return response()->json($post, 201);
    }

    #[Route('GET', '{id}')]
    public function show($id)
    {
        return Post::with('user', 'postCategory')->findOrFail($id);
    }

    #[Route('POST|PUT', '{id}')]
    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);
        $post->update($request->all());
        return $post;
    }

    #[Route('DELETE', '{id}')]
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();
        return response()->json(['message' => 'Post deleted']);
    }
}
