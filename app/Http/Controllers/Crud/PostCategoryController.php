<?php

namespace App\Http\Controllers\Crud;

use App\Http\Controllers\Controller;
use App\Models\PostCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Spatie\RouteDiscovery\Attributes\Route;

class PostCategoryController extends Controller
{
    public function index()
    {
        $postCategory = Cache::remember('bookmark', 3600, function() {
            return PostCategory::all();
        });
        return $postCategory; // Menampilkan semua kategori
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        return PostCategory::create($request->all());
    }

    #[Route('GET', '{id}')]
    public function show($id)
    {
        return PostCategory::findOrFail($id);
    }

    #[Route('POST|PUT', '{id}')]
    public function update(Request $request, $id)
    {
        $category = PostCategory::findOrFail($id);
        $category->update($request->all());
        return $category;
    }

    #[Route('DELETE', '{id}')]
    public function destroy($id)
    {
        $category = PostCategory::findOrFail($id);
        $category->delete();
        return response()->json(['message' => 'Category deleted']);
    }
}
