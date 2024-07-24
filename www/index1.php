<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>SteamDB</title>
    <link rel="stylesheet" href="../www/css/style.css">
    <style>
        /* CSS-Regeln für die Seite */
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh; /* Die Mindesthöhe des Body-Elements entspricht der Höhe des Viewports */
        }

        main {
            flex: 1; /* Der Hauptinhalt dehnt sich aus, um den verfügbaren Platz zwischen dem Header und dem Footer auszufüllen */
        }

        footer {
            text-align: center; /* Zentriere den Text im Footer */
            padding: 20px; /* Füge einen Innenabstand hinzu */
            margin-top: auto; /* Setze den oberen Außenabstand auf "auto", um den Footer an den unteren Rand der Seite zu drücken */
        }
    </style>
</head>
<body>
<a href="index1.php"><img src="../www/bilder/logo.png" alt="logo" id="logo"></a>
<form id="login-form">
    Mail: <input type="text" name="email" id="email" required><br><br>
    Passwort: <input type="password" name="passwort" id="passwort" required><br><br>
    <input type="submit" id="submit" value="Anmelden"><br><br>
</form>
<form action="users.php" method="GET">
    <input type="submit" style="width: 101%; height: 40px; background: transparent; border-color: transparent; color: blueviolet; font-size: 20px;" value="Registrieren">
</form>
<footer>
    <p id="Authors">Authors: Mohammad Freej <br> Dario Kasumovic Carballeira <br> Mohammad Jalal Mobasher Goljani <br> Katherina Nolte</p>
    <p id="Mail"><a href="mailto:hege@example.com">hege@example.com</a></p>
</footer>

<script>
document.getElementById('login-form').addEventListener('submit', function(event) {
    event.preventDefault();

    const email = document.getElementById('email').value;
    const passwort = document.getElementById('passwort').value;

    fetch('http://127.0.0.1:5000/api/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ email: email, passwort: passwort })
    })
    .then(response => response.json())
    .then(data => {
        if (data.message) {
            alert('Login erfolgreich');
            window.location.href = 'mainpage.php';
        } else {
            alert('Fehler: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Fehler:', error);
    });
});
</script>
</body>
</html>
