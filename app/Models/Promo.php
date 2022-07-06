<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    use HasFactory;

    protected $table = 'promo';

    protected $fillable = [
        'title',
        'type_id',
        'begin_date',
        'end_date',
        'image',
        'imageBanner',
        'status_id',
        'awards_qty',
        'description',
    ];

    public function quizzes(){
        return $this->hasMany(Quiz::class);
    }

}
