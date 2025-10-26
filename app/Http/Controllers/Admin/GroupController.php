<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use Illuminate\Http\Request;

class GroupController extends Controller {
    public function index() {
        $groups = Group::orderBy('name')->get();
        return view('admin.groups.index', compact('groups'));
    }
    public function store(Request $r) {
        $data = $r->validate(['name'=>['required','string','max:50','unique:groups,name']]);
        Group::create($data);
        return back()->with('ok','Klasse erstellt.');
    }
    public function update(Request $r, Group $group) {
        $data = $r->validate([
            'name'=>['required','string','max:50','unique:groups,name,'.$group->id],
            'label'=>['nullable','string','max:100'],
            'active'=>['sometimes','boolean'],
        ]);
        $group->update($data);
        return back()->with('ok','Gespeichert.');
    }
}
