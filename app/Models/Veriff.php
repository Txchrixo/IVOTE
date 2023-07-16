<?php

namespace App\Models;


use DateTime;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Veriff extends Model
{

	protected $table = 'verification_sessions';

    protected $fillable = [
        'id',
        'sessionId',
        'hmacSignature',
    ];

}
