<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable {
    use Notifiable, SoftDeletes;

    protected $fillable = ['username','password','role','display_name'];
    protected $hidden = ['password','remember_token'];

    public function groups() {
        return $this->belongsToMany(Group::class)->withPivot('is_primary')->withTimestamps();
    }
    public function entries() {
        return $this->hasMany(Entry::class, 'created_by');
    }
    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isTeacher(): bool { return $this->role === 'teacher'; }
    public function isStudent(): bool { return $this->role === 'student'; }
}
