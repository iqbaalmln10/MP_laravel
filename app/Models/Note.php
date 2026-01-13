<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Note extends Model
{
    protected $fillable = ['user_id', 'project_id', 'title', 'content', 'color'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}