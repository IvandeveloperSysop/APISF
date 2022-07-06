<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type_id',
        'promo_id',
    ];

    public function type(){
        return $this->belongsTo(Type::class, 'type_id', 'id');
    }

    public function promo(){
        return $this->belongsTo(Promo::class, 'promo_id', 'id');
    }


    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function userQuizzes(){
        return $this->hasMany(UserQuiz::class);
    }

}
