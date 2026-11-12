<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
    'user_id', 'name', 'icon','color'
    ];

    public function user():BelongsTo{
       return $this->belongsTo(related:User::class);
    }

    public function expenses():HasMany{
        return $this->hasMany(related:Expense::class);
    }

    public function budgets():HasMany{
        return $this->hasMany(related:Budget::class);
    }

    //Helper methods
    public function getTotalSpentForMonth($month, $year): float{
        return $this->expenses()
        ->whereMonth(column:'date', operator:$month)
        ->whereYear(column:'date', operator:$year)
        ->sum(column: 'amoun');

    }

    
}
