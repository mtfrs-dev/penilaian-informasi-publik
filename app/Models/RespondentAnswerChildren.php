<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RespondentAnswerChildren extends Model
{
    protected $table = 'respondent_answer_children';

    protected $fillable = [
        'respondent_answer_id',
        'respondent_id',
        'question_children_id',
        'question_id',
        'answer',
        'attachment'
    ];

    public function respondent(): BelongsTo
    {
        return $this->belongsTo(User::class,'respondent_id','id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(QuestionChildren::class,'question_children_id','id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(RespondentAnswer::class,'respondent_answer_id','id');
    }
}
