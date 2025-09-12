<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Project;

class Container extends Model
{
    /** @use HasFactory<\Database\Factories\ContainerFactory> */
    use HasFactory;
  
    public function projetos()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }  
    
    public function volumes()
    {
        return $this->hasMany(Volume::class);
    }

    public function network()
    {
        return $this->hasMany(Network::class);
    }    
    
}
