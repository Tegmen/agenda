<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model {
    protected $fillable = ['name','label','active'];
    public function users() { return $this->belongsToMany(User::class)->withPivot('is_primary')->withTimestamps(); }
    public function entries() { return $this->hasMany(Entry::class); }
}
