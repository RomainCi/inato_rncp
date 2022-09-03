<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <p>Bonjour {{$nom}},</p>
    <p>Veuillez cliquer sur lien ci dessous pour verifier votre adresse email</p>
    <a href="http://127.0.0.1:8000/inscription/{{$token}}">Lien de verification</a>
    <p>Ceci est un email automatique pour toute question sur inato veuillez vous rendre dans la partie contact</p>
    <p>Cordialment l'équipe inato</p>
</body>
</html>