<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cash extends Model
{
    use HasFactory;

    protected $table = 'cash';
    protected $fillable = ['debit', 'credit', 'balance', 'type', 'description'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
