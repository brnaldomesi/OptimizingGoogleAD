<?php

namespace App;

use Alsofronie\Uuid\UuidModelTrait;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Cashier\Billable;

class User extends Authenticatable
{
    use Billable;
    use Notifiable;
    use UuidModelTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'date_range',
        'current_account_id',
    ];

    protected $casts = ['admin' => 'boolean'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($user) {

            //cascade delete
            $user->accounts->each(function ($child) {
                $child->delete();
            });

            $user->recents->each(function ($child) {
                $child->delete();
            });
        });
    }

    public function isAdmin()
    {
        return $this->admin;
    }

    public function accounts()
    {
        return $this->hasMany(\App\Models\Account::class)->orderBy('name');
    }

    public function recents()
    {
        return $this->hasMany(\App\Models\Recent::class);
    }
}
