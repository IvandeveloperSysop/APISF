<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserQuiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'quiz_id',
        'total_points',
        'minigame_score_id',
    ];

    public function quiz(){
        return $this->belongsTo(Quiz::class, 'quiz_id', 'id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function miniGame(){
        return $this->belongsTo(MiniGame::class, 'minigame_score_id', 'id');
    }

    public function userQuizzes(){
        return $this->hasMany(UserQuizzes::class);
    }

}
