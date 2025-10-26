<!doctype html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Agenda</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
:root{
  --c-bg:#f7f7f9; --c-fg:#222; --c-muted:#666;
  --type-hausaufgabe:#1e88e5; --type-pruefung:#e53935; --type-unterschrift:#6d4c41; --type-inl:#8e24aa; --type-ereignis:#43a047;
}
body{font:16px/1.4 system-ui,Segoe UI,Roboto,Helvetica,Arial,sans-serif;background:var(--c-bg);color:var(--c-fg);margin:0}
header,main,footer{max-width:980px;margin:auto;padding:12px}
nav a{margin-right:12px}
.grid-week{display:grid;gap:8px;grid-template-columns:repeat(7,minmax(0,1fr))}
@media (max-width:860px){ .grid-week{grid-template-columns:1fr} }
.entry{background:#fff;border-radius:10px;padding:10px;border:1px solid #e5e7eb}
.entry header{display:flex;justify-content:space-between;align-items:center}
.entry h3{margin:0;font-size:1rem}
.entry small{color:var(--c-muted)}
.is-hidden{opacity:.6;text-decoration:line-through}
.type-Hausaufgabe{border-left:6px solid var(--type-hausaufgabe)}
.type-Prüfung{border-left:6px solid var(--type-pruefung)}
.type-Unterschrift{border-left:6px solid var(--type-unterschrift)}
.type-InL{border-left:6px solid var(--type-inl)}
.type-Ereignis{border-left:6px solid var(--type-ereignis)}
</style>
</head>
<body>
<header>
  <nav>
    <a href="{{ route('home') }}">Nächste 10</a>
    <a href="{{ route('weekly') }}">Woche</a>
    @can('admin-only')<a href="/admin/users">Admin</a>@endcan
  </nav>
  <form method="post" action="/logout" style="float:right">@csrf<button>Abmelden</button></form>
</header>
<main>
  @if(session('ok')) <div class="entry">{{ session('ok') }}</div> @endif
  @yield('content')
</main>
</body>
</html>
