<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;


class User extends  Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'nickname',
        'password',
        'role',
        'bitcoin_priv_key',
        'bitcoin_address',
        'eth_priv_key',
        'eth_address',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password'
    ];


    public function setUsername($username)
    {
        $this->update([
            'username' => $username
        ]);
    }


    public function getBitcoinAddress()
    {
        return $this->bitcoin_address;
    }

    public function setBitcoinAddress($bitcoin_address)
    {
        $this->update([
            'bitcoin_address' => $bitcoin_address
        ]);
    }


    public function setBitcoinPrivKey($key)
    {
        $this->update([
            'bitcoin_priv_key' => $key
        ]);
    }

    public function setPassword($password)
    {
        $this->update([
            'password' => password_hash($password,PASSWORD_DEFAULT)
        ]);
    }

}
