<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'answer',
        'correct_answer',
        'question_id',
    ];

    public function question(){
        return $this->belongsTo(Questions::class, 'question_id', 'id');
    }
}
