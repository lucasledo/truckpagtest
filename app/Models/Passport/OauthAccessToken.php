<?php

namespace App\Models\Passport;

use MongoDB\Laravel\Eloquent\Model;

class OauthAccessToken extends Model
{
    protected $connection = 'mongodb';

    protected $fillable = [
        'id',
        'user_id',
        'client_id',
        'name',
        'scopes',
        'revoked',
        'expires_at',
    ];
}
