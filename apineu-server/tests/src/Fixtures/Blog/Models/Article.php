<?php

namespace Neuedev\Apineu\Test\Fixtures\Blog\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Neuedev\Apineu\Eloquent\Model as EloquentModel;

class Article extends EloquentModel
{
    use HasFactory;

    public static $type = 'Blog.Article';

    protected $table = 'articles';

    public $timestamps = false;

    protected $dates = [
        'date'
    ];

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'user', 'tag_users');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'owner');
    }
}
