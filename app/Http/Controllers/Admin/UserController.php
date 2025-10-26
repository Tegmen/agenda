<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller {
    public function index() {
        $users = User::orderBy('username')->paginate(25);
        return view('admin.users.index', compact('users'));
    }

    public function create() {
        $groups = Group::orderBy('name')->get();
        return view('admin.users.form', ['user'=>new User(),'groups'=>$groups]);
    }

    public function store(Request $r) {
        $data = $r->validate([
            'username'=>['required','string','max:50','unique:users,username'],
            'display_name'=>['nullable','string','max:100'],
            'role'=>['required','in:admin,teacher,student'],
            'password'=>['required','string','min:8'],
            'groups'=>['array'],
            'primary_group'=>['nullable','integer'],
        ]);
        $u = User::create([
            'username'=>$data['username'],
            'display_name'=>$data['display_name'] ?? null,
            'role'=>$data['role'],
            'password'=>Hash::make($data['password']),
        ]);
        $sync = [];
        foreach (($data['groups'] ?? []) as $gid) {
            $sync[$gid] = ['is_primary' => ((int)$data['primary_group'] === (int)$gid)];
        }
        $u->groups()->sync($sync);
        return redirect('/admin/users')->with('ok','Benutzer erstellt.');
    }

    public function edit(User $user) {
        $groups = Group::orderBy('name')->get();
        $pivot = $user->groups()->pluck('group_user.is_primary','groups.id')->toArray();
        return view('admin.users.form', compact('user','groups','pivot'));
    }

    public function update(Request $r, User $user) {
        $data = $r->validate([
            'display_name'=>['nullable','string','max:100'],
            'role'=>['required','in:admin,teacher,student'],
            'password'=>['nullable','string','min:8'],
            'groups'=>['array'],
            'primary_group'=>['nullable','integer'],
        ]);
        $user->update([
            'display_name'=>$data['display_name'] ?? null,
            'role'=>$data['role'],
            'password'=>isset($data['password']) && $data['password'] ? Hash::make($data['password']) : $user->password,
        ]);
        $sync = [];
        foreach (($data['groups'] ?? []) as $gid) {
            $sync[$gid] = ['is_primary' => ((int)$data['primary_group'] === (int)$gid)];
        }
        $user->groups()->sync($sync);
        return redirect('/admin/users')->with('ok','Gespeichert.');
    }

    public function disable(User $user) {
        $user->delete();
        return back()->with('ok','Benutzer deaktiviert.');
    }
}
