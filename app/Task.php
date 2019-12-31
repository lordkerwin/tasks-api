<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;

    protected $table = 'tasks';

    protected $fillable = [
        'title',
        'body',
        'due_date',
        'assignee_id',
        'user_id'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
