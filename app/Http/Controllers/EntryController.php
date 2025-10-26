<?php

namespace App\Http\Controllers;

use App\Models\Entry;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Autolink\AutolinkExtension;

class EntryController extends Controller {
    private function markdown(string $text): string {
        $env = new Environment(['disallowed_raw_html' => true]);
        $env->addExtension(new CommonMarkCoreExtension());
        $env->addExtension(new AutolinkExtension());
        $converter = new CommonMarkConverter([], $env);
        return $converter->convert($text)->getContent();
    }

    private function userGroupIds() {
        $u = Auth::user();
        if ($u->isAdmin()) return Group::pluck('id');
        return $u->groups()->pluck('groups.id');
    }

    public function upcoming(Request $r) {
        $groupIds = $this->userGroupIds();
        $sel = $r->integer('group_id') ?: null;

        $q = Entry::query()
            ->when($sel, fn($q)=>$q->where('group_id', $sel),
                   fn($q)=>$q->whereIn('group_id', $groupIds))
            ->whereDate('relevance_date', '>=', Carbon::today('Europe/Zurich'))
            ->orderBy('relevance_date')->orderBy('created_at');

        $u = Auth::user();
        if ($u->isStudent()) {
            $q->where('hidden_for_students', false)->whereNull('superseded_by');
        }

        $entries = $q->limit(10)->get();
        $groups = Group::whereIn('id', $groupIds)->orderBy('name')->get();

        return view('entries.upcoming', compact('entries','groups','sel'));
    }

    public function weekly(Request $r) {
        $groupIds = $this->userGroupIds();
        $sel = $r->integer('group_id') ?: null;

        $date = $r->date('date') ?? Carbon::today('Europe/Zurich');
        $monday = Carbon::parse($date)->startOfWeek(Carbon::MONDAY);
        $sunday = (clone $monday)->endOfWeek(Carbon::SUNDAY);

        $q = Entry::query()
            ->when($sel, fn($q)=>$q->where('group_id',$sel),
                   fn($q)=>$q->whereIn('group_id',$groupIds))
            ->whereBetween('relevance_date', [$monday->toDateString(), $sunday->toDateString()])
            ->orderBy('relevance_date')->orderBy('created_at');

        $u = Auth::user();
        $showHidden = $r->boolean('show_hidden', true);
        if ($u->isStudent()) {
            $q->where('hidden_for_students', false)->whereNull('superseded_by');
        } elseif (!$showHidden) {
            $q->where('hidden_for_students', false)->whereNull('superseded_by');
        }

        $entries = $q->get();
        $groups = Group::whereIn('id', $groupIds)->orderBy('name')->get();

        return view('entries.weekly', compact('entries','groups','sel','monday','sunday','showHidden'));
    }

    public function create() {
        $groups = Auth::user()->isAdmin()
            ? Group::orderBy('name')->get()
            : Auth::user()->groups()->orderBy('name')->get();
        return view('entries.form', ['entry'=>new Entry(),'groups'=>$groups]);
    }

    public function store(Request $r) {
        $u = Auth::user();
        $groups = $u->isAdmin() ? Group::pluck('id') : $u->groups()->pluck('groups.id');

        $data = $r->validate([
            'relevance_date' => ['required','date'],
            'title' => ['required','string','max:40'],
            'description' => ['required','string','max:'.($u->isStudent()?1000:4000)],
            'type' => ['required','in:Hausaufgabe,Prüfung,Unterschrift,InL,Ereignis'],
            'group_id' => ['required','integer','in:'.implode(',',$groups->toArray())],
        ]);

        $data['created_by'] = $u->id;
        $e = Entry::create($data);

        return redirect()->route('home')->with('ok','Eintrag erstellt.');
    }

    public function edit(Entry $entry) {
        $this->authorize('update', $entry);
        $groups = Auth::user()->isAdmin()
            ? Group::orderBy('name')->get()
            : Auth::user()->groups()->orderBy('name')->get();
        return view('entries.form', compact('entry','groups'));
    }

    public function update(Request $r, Entry $entry) {
        $this->authorize('update', $entry);
        $u = Auth::user();
        $groups = $u->isAdmin() ? Group::pluck('id') : $u->groups()->pluck('groups.id');

        $data = $r->validate([
            'relevance_date' => ['required','date'],
            'title' => ['required','string','max:40'],
            'description' => ['required','string','max:'.($u->isStudent()?1000:4000)],
            'type' => ['required','in:Hausaufgabe,Prüfung,Unterschrift,InL,Ereignis'],
            'group_id' => ['required','integer','in:'.implode(',',$groups->toArray())],
        ]);

        if ($u->isStudent()) {
            $new = Entry::create($data + ['created_by'=>$u->id]);
            $entry->update(['superseded_by'=>$new->id, 'hidden_for_students'=>true]);
        } else {
            $entry->update($data);
        }
        return redirect()->route('home')->with('ok','Gespeichert.');
    }

    public function destroy(Entry $entry) {
        $this->authorize('delete', $entry);
        $u = Auth::user();
        if ($u->isStudent()) {
            $entry->update(['hidden_for_students'=>true]);
        } else {
            $entry->delete();
        }
        return back()->with('ok', $u->isStudent() ? 'Ausgeblendet.' : 'Gelöscht.');
    }

    public function adminIndex(Request $r) {
        $q = Entry::query()->with(['group','creator']);

        if ($gid = $r->integer('group_id')) $q->where('group_id',$gid);
        if ($cid = $r->integer('created_by')) $q->where('created_by',$cid);
        if ($type = $r->get('type')) $q->where('type',$type);

        $entries = $q->orderByDesc('created_at')->paginate(25)->withQueryString();
        return view('admin.entries', compact('entries'));
    }
}
