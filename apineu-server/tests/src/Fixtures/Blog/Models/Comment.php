<?php

namespace Neuedev\Apineu\Test\Fixtures\Blog\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Neuedev\Apineu\Eloquent\Model as EloquentModel;

class Comment extends EloquentModel
{
    use HasFactory;

    public static $type = 'Blog.Comment';

    protected $table = 'comments';

    public $timestamps = false;

    public function owner()
    {
        return $this->morphTo('owner');
    }
}
