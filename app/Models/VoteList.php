<?php

namespace App\Models;

use App\Enums\ElectionStage;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    public function getPhase(int $phase): string
    {
        return (ElectionStage::tryFrom($phase) ?? ElectionStage::Initial)->label();
    }

    /**
     * Public URL of the uploaded background (served through the public/storage link),
     * or of the default background.
     */
    public function backgroundUrl(): string
    {
        $file = $this->bg ? basename($this->bg) : 'voting-bg.jpg';

        return asset('storage/uploads/bg/' . rawurlencode($file));
    }

    public function currentStage(): ElectionStage
    {
        return ElectionStage::from((int) $this->stage);
    }

    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class, 'vote_id');
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
            set: fn (string $value) => self::slug($value)
        );
    }

    /** Storage format of election titles. */
    public static function slug(string $title): string
    {
        return str_replace(' ', '-', strtolower(trim($title)));
    }


}
