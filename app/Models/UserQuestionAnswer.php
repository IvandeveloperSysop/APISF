<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserQuestionAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'questions_answer',
        'user_quiz_id',
    ];

    public function question(){
        return $this->belongsTo(Question::class, 'question_id', 'id');
    }

    public function userQuiz(){
        return $this->belongsTo(UserQuiz::class, 'user_quiz_id', 'id');
    }

}
