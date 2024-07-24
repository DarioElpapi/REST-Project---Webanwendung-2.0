<?php

if(isset($_GET['library'])) {

    header("Location: library.php");
    exit;
}

if(isset($_GET['genres'])) {

    header("Location: genres.php");
    exit;
}

if(isset($_GET['addmovie'])) {

    header("Location: add.php");
    exit;
}

if(isset($_GET['addseries'])) {

    header("Location: addseries.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../www/css/style.css">
    <title>SteamDB</title>
</head>
<body>
    <!-- Logo oben rechts -->
    <div id="logo-container">
        <img id="logo" src="../www/bilder/logo.png" alt="logo">
    </div>
    <span onclick="openNav()">&#9776;</span>
    <div id="mySidenav" class="sidenav">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
        <a href="#">
            <img src="../www/bilder/home_icon.png" class="nav-icon">
            Hauptseite
        </a>
        <a href="?library">
            <img src="../www/bilder/library_icon.png" class="nav-icon">
            Meine Liste
        </a>
        <a href="?genres">
            <img src="../www/bilder/genres_icon.png" class="nav-icon">
            Genres
        </a>
        <button class="dropdown-btn" style="padding: 8px 8px 8px 32px;
            text-decoration: none;
            font-size: 20px;
            color: #818181;
            display: block;
            border: none;
            background: none;
            width:100%;
            text-align: left;
            cursor: pointer;
            outline: none;">
            <img src="../www/bilder/add_icon.png" class="nav-icon">
            Add
            <i class="fa fa-caret-down"></i>
        </button>
        <div class="dropdown-container">
            <a href="?addmovie">Add Movie</a>
            <a href="?addseries">Add Series</a>
        </div>
        <a href="javascript:void(0)" onclick="logout()">
            <img src="../www/bilder/logout_icon.png" class="nav-icon">
            Abmelden
        </a>
    </div>
    <h1 id="welcome-message">Willkommen auf der Hauptseite!</h1>
    <p id="user-email"></p>
    
    <footer>
        <p id="Authors">Authors: Mohammad Freej <br> Dario Kasumovic Carballeira <br> Mohammad Jalal Mobasher Goljani <br> Katharina Nolte</p>
        <p id="Mail"><a href="mailto:hege@example.com">dario.carballeira98@web.de</a></p>
    </footer>

    <script>
        function openNav() {
            document.getElementById("mySidenav").style.width = "250px";
        }

        function closeNav() {
            document.getElementById("mySidenav").style.width = "0";
        }

        var dropdown = document.getElementsByClassName("dropdown-btn");
        var i;

        for (i = 0; i < dropdown.length; i++) {
            dropdown[i].addEventListener("click", function() {
                this.classList.toggle("active");
                var dropdownContent = this.nextElementSibling;
                if (dropdownContent.style.display === "block") {
                    dropdownContent.style.display = "none";
                } else {
                    dropdownContent.style.display = "block";
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            fetch('http://127.0.0.1:5000/api/login-status')
                .then(response => response.json())
                .then(data => {
                    if (data.loggedin) {
                        document.getElementById('welcome-message').innerText = 'Willkommen auf der Hauptseite, ' + data.email + '!';
                        document.getElementById('user-email').innerText = 'Eingeloggt als: ' + data.email;
                    } else {
                        alert('Sie sind nicht eingeloggt. Sie werden zur Login-Seite weitergeleitet.');
                        window.location.href = 'index1.php';
                    }
                })
                .catch(error => console.error('Fehler:', error));
        });

        function logout() {
            fetch('http://127.0.0.1:5000/api/logout', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.message) {
                    alert('Logout erfolgreich');
                    window.location.href = 'index1.php';
                } else {
                    alert('Fehler: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Fehler:', error);
            });
        }
    </script>
</body>
</html>
