<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class discussion_comments extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'discussion_comments';

    protected $fillable = [
        'discussion_id',
        'user_id',
        'comment',
    ];

    public function discussion()
    {
        return $this->belongsTo(Discussion::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
