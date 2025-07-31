<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'plan', 
        'status', 
        'starts_at', 
        'ends_at', 
        'stripe_id', 
        'stripe_status', 
        'default_payment_method',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}