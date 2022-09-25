<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    {{-- <link href="{{ asset('/css/style.css') }}" rel="stylesheet">  --}}





    <title>Document</title>
</head>
<body>
    <p>Bonjour {{$nom??null}},</p>
    <p>Veuillez cliquer sur lien ci dessous pour vérifier votre adresse email</p>
    <a href="http://127.0.0.1:8000/inscription/{{$token??null}}">Lien de verification</a>
    <p>Ceci est un email automatique pour toute question sur inato veuillez vous rendre dans la partie contact</p>
    <p>Cordialement l'équipe inato</p>
</body>
</html>