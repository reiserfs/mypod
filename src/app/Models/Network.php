<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Network extends Model
{
    /** @use HasFactory<\Database\Factories\NetworkFactory> */
    use HasFactory;

    public function container()
    {
        return $this->belongsTo(Container::class);
    }    
}
