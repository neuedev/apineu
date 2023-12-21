<?php

namespace Neuedev\Apineu\Test\Fixtures\Blog\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Neuedev\Apineu\Eloquent\Model as EloquentModel;

class Profile extends EloquentModel
{
    use HasFactory;

    public static $type = 'Blog.Profile';

    protected $table = 'profiles';

    public $timestamps = false;
}
