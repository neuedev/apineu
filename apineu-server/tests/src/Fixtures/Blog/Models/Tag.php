<?php

namespace Neuedev\Apineu\Test\Fixtures\Blog\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Neuedev\Apineu\Eloquent\Model as EloquentModel;

class Tag extends EloquentModel
{
    use HasFactory;

    public static $type = 'Blog.Tag';

    protected $table = 'tags';

    public $timestamps = false;

    public function authors()
    {
        return $this->morphedByMany(Author::class, 'user', 'tag_users');
    }

    public function articles()
    {
        return $this->morphedByMany(Article::class, 'user', 'tag_users');
    }

    public function tag_users()
    {
        return $this->hasMany(TagUser::class);
    }
}
