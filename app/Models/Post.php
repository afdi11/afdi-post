<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Post extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = ['title', 'content', 'user_id', 'post_category_id'];

    protected $appends = ['media_url'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function postCategory()
    {
        return $this->belongsTo(PostCategory::class);
    }

    public function bookmarks()
    {
        return $this->belongsToMany(User::class, 'bookmarks');
    }

    public function getMediaUrlAttribute()
    {
        return $this->getFirstMediaUrl('images');
    }

}
