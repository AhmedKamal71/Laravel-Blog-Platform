<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory;
    public function User()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
    protected $fillable = [
        'title',
        'content',
        'category',
        'author_id',
    ];
}
