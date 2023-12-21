<?php

namespace Neuedev\Apineu\Test\Fixtures\Blog\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Neuedev\Apineu\Eloquent\Model as EloquentModel;

class Link extends EloquentModel
{
    use HasFactory;

    public static $type = 'Blog.Link';

    protected $table = 'links';

    public $timestamps = false;

    public function author()
    {
        return $this->belongsTo(Author::class);
    }
}
