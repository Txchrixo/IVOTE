<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ballot extends Model
{
    protected $casts = ['signature' => 'array'];

    protected $hidden = ['bitcoin_transaction'];
}
