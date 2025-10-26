@extends('layout')
@section('content')
<form method="post" action="/login" class="entry">@csrf
  <h3>Anmelden</h3>
  <label>Benutzername <input name="username" required value="{{ old('username') }}"></label><br>
  <label>Passwort <input type="password" name="password" required></label><br>
  <label><input type="checkbox" name="remember" value="1"> Eingeloggt bleiben</label><br>
  @error('username')<div>{{ $message }}</div>@enderror
  <button>Anmelden</button>
</form>
@endsection
