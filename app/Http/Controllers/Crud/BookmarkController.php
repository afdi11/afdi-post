<?php

namespace App\Http\Controllers\Crud;

use App\Http\Controllers\Controller;
use App\Models\Bookmark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BookmarkController extends Controller
{
    public function index()
    {
        $bookmark = Cache::remember('bookmark', 3600, function() {
            return Bookmark::with('user', 'post')->get();
        });
        return $bookmark; // Menampilkan semua bookmark
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'post_id' => 'required|exists:posts,id',
        ]);

        return Bookmark::create($request->all());
    }

    public function destroy($id)
    {
        $bookmark = Bookmark::findOrFail($id);
        $bookmark->delete();
        return response()->json(['message' => 'Bookmark deleted']);
    }
}
