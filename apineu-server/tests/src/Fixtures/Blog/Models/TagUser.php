<?php

namespace Neuedev\Apineu\Test\Fixtures\Blog\Models;

use Neuedev\Apineu\Eloquent\Model as EloquentModel;

class TagUser extends EloquentModel
{
    public static $type = 'Blog.TagUser';

    protected $table = 'tag_users';

    public function user()
    {
        return $this->morphTo();
    }
}
