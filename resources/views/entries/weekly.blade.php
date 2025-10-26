@extends('layout')
@section('content')
<form method="get" class="entry">
  <label>Klasse:
    <select name="group_id">
      <option value="">Alle Klassen</option>
      @foreach($groups as $g)
        <option value="{{ $sel==$g->id ? $g->id : $g->id }}" @selected($sel==$g->id)>{{ $g->name }}</option>
      @endforeach
    </select>
  </label>
  <label>Datum: <input type="date" name="date" value="{{ $monday->toDateString() }}"></label>
  @if(auth()->user()->isTeacher() || auth()->user()->isAdmin())
    <label><input type="checkbox" name="show_hidden" value="1" {{ $showHidden?'checked':'' }}> Versteckte anzeigen</label>
  @endif
  <button>Anzeigen</button>
  <a href="/eintrag/neu">Neuer Eintrag</a>
</form>

<div class="grid-week">
@php
$days = [];
for($i=0;$i<7;$i++){ $d=$monday->copy()->addDays($i); $days[$d->toDateString()] = $d; }
$byDay = $entries->groupBy(fn($e)=>$e->relevance_date->toDateString());
@endphp
@foreach($days as $k=>$d)
  <section>
    <h3>{{ $d->locale('de_CH')->isoFormat('dd, DD.MM.') }}</h3>
    @forelse(($byDay[$k] ?? collect()) as $e)
      <article class="entry type-{{ $e->type }} {{ ($e->hidden_for_students || $e->superseded_by)?'is-hidden':'' }}">
        <h4 style="margin:0">{{ $e->title }}</h4>
        <div class="md">{!! app(\App\Http\Controllers\EntryController::class)->markdown($e->description) !!}</div>
        <small>{{ $e->type }} · {{ $e->group->name }} · {{ $e->creator->display_name ?? $e->creator->username }}</small>
        @can('update', $e) <div><a href="/eintrag/{{ $e->id }}/bearbeiten">Bearbeiten</a></div> @endcan
      </article>
    @empty
      <div class="entry">–</div>
    @endforelse
  </section>
@endforeach
</div>
@endsection
