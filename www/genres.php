<?php
// include 'variables.php';

session_start();

if(isset($_GET['library'])) {

    header("Location: library.php");
    exit;
}

if(isset($_GET['mainpage'])) {

    header("Location: mainpage.php");
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
        <a href="?mainpage">
            <img src="../www/bilder/home_icon.png" class="nav-icon">
            Hauptseite
            </a>
            <a href="?library">
                <img src="../www/bilder/library_icon.png" class="nav-icon">
                Meine Liste
            </a>
            <a href="#">
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

    <form>
    <!-- Formular für Genre-Auswahl -->
    <label for="genre">Genre auswählen:</label>
    <select name="genre_id" id="genre" onchange="storeGenreId()">
        <!-- Optionen werden durch JavaScript hinzugefügt -->
    </select>
    <!-- Button zum Anzeigen der Filme -->
    <button type="button" onclick="showMovies(); showSeries();">Anzeigen</button>
    </form>

    <!-- Container für die Filme -->
    <div>
        <h2>Movies</h2>
        <table id="movies">
            <thead>
                <tr>
                    <th>Titel</th>
                    <th>Erscheinungsjahr</th>
                    <th>Dauer</th>
                    <th>IMDb-Link</th>
                    <th>Score</th>
                </tr>
            </thead>
            <tbody>
                <!-- Filmdaten werden hier dynamisch eingefügt -->
            </tbody>
        </table>
    </div>

    <!-- Container für die Serien -->
    <div>
        <h2>Serien</h2>
        <table id="series">
            <thead>
                <tr>
                    <th>Titel</th>
                    <th>Erscheinungsjahr</th>
                    <th>Staffeln</th>
                    <th>IMDb-Link</th>
                    <th>Score</th>
                </tr>
            </thead>
            <tbody>
                <!-- Seriendas werden hier dynamisch eingefügt -->
            </tbody>
        </table>
    </div>
    
    <footer>
        <p id="Authors">Authors: Mohammad Freej <br> Dario Kasumovic Carballeira <br> Mohammad Jalal Mobasher Goljani <br> Katharina Nolte</p>
        <p id="Mail"><a href="mailto:hege@example.com">dario.carballeira98@www.de</a></p>
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


        var genreId = null;
        document.addEventListener('DOMContentLoaded', function() {
            // Fetch genres from the API and populate the genre select box
            fetch('http://127.0.0.1:5000/api/genres')
                .then(response => response.json())
                .then(data => {
                    const genreSelect = document.getElementById('genre');
                    genreID = genreSelect;
                    data.forEach(genre => {
                        const option = document.createElement('option');
                        option.value = genre.id;
                        option.textContent = genre.genre;
                        genreSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error fetching genres:', error));
        });


        function storeGenreId() {
            // Funktion zum Speichern der genre_id in einer globalen Variablen
            genreId = document.getElementById("genre").value;
            alert(genreId);
        }

        function showMovies() {
        // Funktion zum Anzeigen der Filme für das ausgewählte Genre
        if (genreId === null) {
            alert("Bitte wählen Sie ein Genre aus.");
            return;
        }
        fetch("http://localhost:5000/api/genres/show/" + genreId)
            .then(response => response.json())
            .then(movies => {
                var moviesTable = document.getElementById("movies").getElementsByTagName('tbody')[0];
                moviesTable.innerHTML = ""; // Clear existing content
                if (movies.length > 0) {
                    movies.forEach(movie => {
                        let row = moviesTable.insertRow();
                        row.insertCell(0).textContent = movie.title;
                        row.insertCell(1).textContent = movie.erscheinungsjahr;
                        row.insertCell(2).textContent = movie.dauer;
                        let linkCell = row.insertCell(3);
                        let link = document.createElement('a');
                        link.href = movie.link;
                        link.textContent = movie.link;
                        linkCell.appendChild(link);
                        row.insertCell(4).textContent = movie.bewertung;
                    });
                } else {
                    let row = moviesTable.insertRow();
                    let cell = row.insertCell(0);
                    cell.colSpan = 6;
                    cell.textContent = 'Keine Filme in der Favoritenliste gefunden.';
                }
            })
            .catch(error => console.error('Error fetching movies:', error));
        }

        function showSeries() {
        if (genreId === null) {
            alert("Bitte wählen Sie ein Genre aus.");
            return;
        }
        fetch("http://localhost:5000/api/genres/shows/" + genreId)
            .then(response => response.json())
            .then(series => {
                var seriesTable = document.getElementById("series").getElementsByTagName('tbody')[0];
                seriesTable.innerHTML = ""; // Clear existing content
                if (series.length > 0) {
                    series.forEach(serie => {
                        let row = seriesTable.insertRow();
                        row.insertCell(0).textContent = serie.title;
                        row.insertCell(1).textContent = serie.erscheinungsjahr;
                        row.insertCell(2).textContent = serie.staffeln;
                        let linkCell = row.insertCell(3);
                        let link = document.createElement('a');
                        link.href = serie.link;
                        link.textContent = serie.link;
                        linkCell.appendChild(link);
                        row.insertCell(4).textContent = serie.bewertung;
                    });
                } else {
                    let row = seriesTable.insertRow();
                    let cell = row.insertCell(0);
                    cell.colSpan = 6;
                    cell.textContent = 'Keine Serien in der Favoritenliste gefunden.';
                }
            })
            .catch(error => console.error('Error fetching series:', error));
        }

    </script>
</body>
</html>
