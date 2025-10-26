<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entry extends Model {
    protected $fillable = [
        'relevance_date','title','description','type','group_id','created_by','hidden_for_students','superseded_by'
    ];

    protected $casts = ['relevance_date' => 'date', 'hidden_for_students' => 'boolean'];

    public function group() { return $this->belongsTo(Group::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function supersededBy() { return $this->belongsTo(Entry::class, 'superseded_by'); }
}
