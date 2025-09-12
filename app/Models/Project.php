<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Container;

class Project extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory;

    // public function users()
    // {
    //     return $this->belongsTo(User::class, 'user_id');
    // }    

    public function containers()
    {
        return $this->hasMany(Container::class);
    } 
   
}
