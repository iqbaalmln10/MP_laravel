<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Activity extends Model
{
    protected $fillable = ['user_id', 'description', 'subject_title', 'type', 'action'];

    public static function log($description, $subject_title, $type, $action)
    {
        return self::create([
            'user_id' => Auth::id(),
            'description' => $description,
            'subject_title' => $subject_title,
            'type' => $type,
            'action' => $action,
        ]);
    }
}
