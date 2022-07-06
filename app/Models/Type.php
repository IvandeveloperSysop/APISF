<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    use HasFactory;

    protected $table = 'type_catalog';

    protected $fillable = [
        'name',
        'table',
    ];

    public function questions(){
        return $this->hasMany(Question::class);
    }
    
    public function quizzes(){
        return $this->hasMany(Quiz::class);
    }

}
