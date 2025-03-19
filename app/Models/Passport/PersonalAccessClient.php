<?php

namespace App\Models\Passport;

use MongoDB\Laravel\Eloquent\Model;

class PersonalAccessClient extends Model
{
    protected $connection = 'mongodb';

    protected $fillable = [
        'id',
        'user_id',
        'name',
        'secret',
        'redirect',
        'personal_access_client',
        'password_client',
        'revoked',
    ];
}
