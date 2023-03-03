<?php

namespace App\Models;
use Alsofronie\Uuid\UuidModelTrait;
use Illuminate\Database\Eloquent\Model;

class EmailSubscriber extends Model
{
    use UuidModelTrait;
    protected $table = 'email_subscribers';

}
