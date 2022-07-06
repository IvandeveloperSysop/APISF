<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'image',
        'quiz_id',
        'type_id',
        'order',
    ];

    public function quiz(){
        return $this->belongsTo(Quiz::class, 'quiz_id', 'id');
    }

    public function type(){
        return $this->belongsTo(Type::class, 'type_id', 'id');
    }

    public function questionAnswers(){
        return $this->hasMany(QuestionAnswer::class);
    }

    public function userQuestionAnswer(){
        return $this->hasMany(UserQuestionAnswer::class);
    }

}
