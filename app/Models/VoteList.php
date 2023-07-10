<?php

namespace App\Models;

use App\Enums\StageElection;
use DateTime;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoteList extends Model
{

	protected $table = 'vote';

    protected $fillable = [
        'id',
        'title',
        'number',
        'description',
        'stage',
        'bg',
        'start_vote',
        'end_vote',
        'start_veriff',
        'end_veriff',
    ];

    public function setProfile($id,$title,$number,$description)
    {
        $this->where('id',$id)->update([
            'title' => $title,
            'number' => $number,
            'description' => $description
        ]);
    }

    public function getPhase(int $phase) : string
    {
        if (StageElection::$veriff == $phase){
            return 'veriff';
        }
        if (StageElection::$waiting == $phase){
            return 'waiting';
        }
        if (StageElection::$ongoing == $phase){
            return 'ongoing';
        }
        if (StageElection::$done == $phase){
            return 'done';
        }

        return 'initial';
    }

    /**
     * Mutateur pour le titre.
     *
     * @param  string  $value
     * @return void
     */
    protected function title(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => str_replace('-', ' ', ucfirst(strtolower($value))),
            set: fn (string $value) => str_replace(' ', '-', strtolower($value))
        );
    }


}
