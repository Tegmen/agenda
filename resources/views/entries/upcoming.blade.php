@extends('layout')
@section('content')
<form method="get" class="entry">
  <label>Klasse:
    <select name="group_id" onchange="this.form.submit()">
      <option value="">Alle Klassen</option>
      @foreach($groups as $g)
        <option value="{{ $g->id }}" @selected($sel==$g->id)>{{ $g->name }}</option>
      @endforeach
    </select>
  </label>
  <a href="/eintrag/neu">Neuer Eintrag</a>
</form>

@foreach($entries as $e)
<article class="entry type-{{ $e->type }} {{ ($e->hidden_for_students || $e->superseded_by)?'is-hidden':'' }}">
  <header>
    <h3>{{ $e->title }}</h3>
    <time datetime="{{ $e->relevance_date->toDateString() }}">{{ $e->relevance_date->format('D d.m.') }}</time>
  </header>
  <div class="md">{!! app(\App\Http\Controllers\EntryController::class)->markdown($e->description) !!}</div>
  <footer>
    <small>{{ $e->type }} · {{ $e->group->name }} · {{ $e->creator->display_name ?? $e->creator->username }}</small>
    @can('update', $e) <a href="/eintrag/{{ $e->id }}/bearbeiten">Bearbeiten</a> @endcan
    @can('delete', $e)
    <form method="post" action="/eintrag/{{ $e->id }}" style="display:inline" onsubmit="return confirm('Wirklich löschen/ausblenden?')">
      @csrf @method('DELETE')
      <button>{{ auth()->user()->isStudent() ? 'Ausblenden' : 'Löschen' }}</button>
    </form>
    @endcan
  </footer>
</article>
@endforeach
@endsection
