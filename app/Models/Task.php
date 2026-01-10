<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    // Agar kolom bisa diisi saat proses Task::create()
    protected $fillable = ['project_id', 'title', 'due_date', 'is_completed'];

    // Relasi balik: Satu tugas dimiliki oleh satu proyek
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}