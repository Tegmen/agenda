@extends('layout')
@section('content')
<form method="post" action="{{ $entry->exists ? '/eintrag/'.$entry->id : '/eintrag' }}" class="entry">
  @csrf
  @if($entry->exists) @method('PUT') @endif
  <label>Datum (Relevanz)
    <input type="date" name="relevance_date" required value="{{ old('relevance_date', optional($entry->relevance_date)->toDateString()) }}">
  </label><br>
  <label>Titel
    <input name="title" maxlength="40" required value="{{ old('title',$entry->title) }}">
  </label><br>
  <label>Typ
    <select name="type" required>
      @foreach(['Hausaufgabe','Prüfung','Unterschrift','InL','Ereignis'] as $t)
        <option value="{{ $t }}" @selected(old('type',$entry->type)==$t)>{{ $t }}</option>
      @endforeach
    </select>
  </label><br>
  <label>Klasse
    <select name="group_id" required>
      @foreach($groups as $g)
        <option value="{{ $g->id }}" @selected(old('group_id',$entry->group_id)==$g->id)>{{ $g->name }}</option>
      @endforeach
    </select>
  </label><br>
  <label>Beschreibung (Markdown)
    <textarea name="description" rows="6" maxlength="{{ auth()->user()->isStudent()?1000:4000 }}" required>{{ old('description',$entry->description) }}</textarea>
  </label><br>
  <button>Speichern</button>
</form>
@endsection
