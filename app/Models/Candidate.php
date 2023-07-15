<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    use HasFactory;
    protected $table = 'candidate';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'des',
        'cni',
        'photo',
        'vote_id',
    ];

    /** The national identity number is never exposed in JSON. */
    protected $hidden = ['cni'];

    public function addCandidate($name,$description,$vote_id){
        $this->create([
            'name' => $name,
            'des' => $description,
            'vote_id' => $vote_id
        ]);
    }

    public function updateCandidate($name,$description){
        $this->update([
            'name' => $name,
            'des' => $description
        ]);
    }

    public function delCandidate($id){
        $this->where('id',$id)->delete();
    }
}
