<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <p>Bonjour,</p>
    <p>Voici votre nouveau mot de passe {{$password}}</p>
    <p>Veuillez cliquer sur le lien ci dessous pour valider votre nouveau mot de passe</p>
    <a href="http://127.0.0.1:8000/reset/password/{{$token}}">Lien</a>
    <p>Si vous êtes pas à l'origine de cette action ne faite rien </p>
    <p>Ceci est un email automatique pour toute question sur inato veuillez vous rendre dans la partie contact</p>
    <p>Cordialment l'équipe inato</p>
</body>
</html>