<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
	@vite('resources/css/app.css')
	@vite('resources/js/app.js')

  <title>
    Termo
  </title>
  
</head>
<body>
	<div id="app" class="-z-10 bg-zinc-950 text-zinc-200 min-h-screen before:content[''] before:block before:absolute before:top-1/2 before:left-1/2 before:-translate-1/2 before:size-full before:rounded-full before:bg-radial before:from-indigo-500/5 before:to-zinc-950/5 before:to-50% before:z-0">
    <App />
  </div>
  
</body>
</html>