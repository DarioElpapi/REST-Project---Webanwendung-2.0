<?php
// include 'variables.php';

if(isset($_GET['mainpage'])) {
    header("Location: mainpage.php");
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
    <title>Favoritenliste</title>
    <link rel="stylesheet" href="../www/css/style.css">
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
        <a href="#">
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
            width: 100%;
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

    <h1>Favoritenliste</h1>

    <h2>Movies</h2>
    <table id="movies">
        <thead>
            <tr>
                <th>Titel</th>
                <th>Erscheinungsjahr</th>
                <th>Genre</th>
                <th>Dauer</th>
                <th>IMDb-Link</th>
                <th>Score</th>
                <th>Aktionen</th>
            </tr>
        </thead>
        <tbody>
            <!-- Filmdaten werden hier dynamisch eingefügt -->
        </tbody>
    </table>

    <h2>Serien</h2>
    <table id="series">
        <thead>
            <tr>
                <th>Titel</th>
                <th>Erscheinungsjahr</th>
                <th>Genre</th>
                <th>Staffeln</th>
                <th>IMDb-Link</th>
                <th>Score</th>
            </tr>
        </thead>
        <tbody>
            <!-- Seriendas werden hier dynamisch eingefügt -->
        </tbody>
    </table>

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

        document.addEventListener('DOMContentLoaded', function() {
    // JavaScript-Map für die Zuordnung von Zeilennummer zu Movie-ID
    var movieIdMap = new Map();
    var serieIdMap = new Map();

    fetch('http://127.0.0.1:5000/api/movies/show')
        .then(response => response.json())
        .then(data => {
            let moviesTable = document.getElementById('movies').getElementsByTagName('tbody')[0];
            if (data.length > 0) {
                data.forEach((movie, index) => {
                    let row = moviesTable.insertRow();
                    row.id = `movie-${index}`;
                    // Speichern Sie die Movie-ID in der Map unter der Zeilennummer
                    movieIdMap.set(index, movie.id);
                    row.insertCell(0).textContent = movie.title;
                    row.insertCell(1).textContent = movie.erscheinungsjahr;
                    row.insertCell(2).textContent = movie.genre;
                    row.insertCell(3).textContent = movie.dauer;
                    let linkCell = row.insertCell(4);
                    let link = document.createElement('a');
                    link.href = movie.link;
                    link.textContent = movie.link;
                    linkCell.appendChild(link);
                    row.insertCell(5).innerHTML = movie.bewertung + '<img src="../www/bilder/star.png" alt="star" width="25px" height="25px">';
                    let actionsCell = row.insertCell(6);
                    let deleteButton = document.createElement('button');
                    deleteButton.textContent = 'Löschen';
                    // Rufen Sie delete_movie mit der Zeilennummer auf, um die entsprechende Movie-ID zu erhalten
                    deleteButton.onclick = () => delete_movie(index);
                    actionsCell.appendChild(deleteButton);
                });
            } else {
                let row = moviesTable.insertRow();
                let cell = row.insertCell(0);
                cell.colSpan = 7;
                cell.textContent = 'Keine Filme in der Favoritenliste gefunden.';
            }
        })
        .catch(error => console.error('Error fetching movies:', error));

        fetch('http://127.0.0.1:5000/api/series/show')
                .then(response => response.json())
                .then(data => {
                    let seriesTable = document.getElementById('series').getElementsByTagName('tbody')[0];
                    if (data.length > 0) {
                        data.forEach((series, index) => {
                            let row = seriesTable.insertRow();
                            row.id = `serie-${index}`;
                            // Speichern Sie die Movie-ID in der Map unter der Zeilennummer
                            serieIdMap.set(index, series.id);
                            row.insertCell(0).textContent = series.title;
                            row.insertCell(1).textContent = series.erscheinungsjahr;
                            row.insertCell(2).textContent = series.genre;
                            row.insertCell(3).textContent = series.staffeln;
                            let linkCell = row.insertCell(4);
                            let link = document.createElement('a');
                            link.href = series.link;
                            link.textContent = series.link;
                            linkCell.appendChild(link);
                            row.insertCell(5).innerHTML = series.bewertung + '<img src="../www/bilder/star.png" alt="star" width="25px" height="25px">';
                            
                            let actionsCell = row.insertCell(6);
                            let deleteButton = document.createElement('button');
                            deleteButton.textContent = 'Löschen';
                            // Rufen Sie delete_movie mit der Zeilennummer auf, um die entsprechende Movie-ID zu erhalten
                            deleteButton.onclick = () => delete_serie(index);
                            actionsCell.appendChild(deleteButton);
                        });
                    } else {
                        let row = seriesTable.insertRow();
                        let cell = row.insertCell(0);
                        cell.colSpan = 7;
                        cell.textContent = 'Keine Serien in der Favoritenliste gefunden.';
                    }
                })
                .catch(error => console.error('Error fetching series:', error));

    // Funktion zum Löschen eines Films
    function delete_movie(rowNumber) {
        // Holen Sie die Movie-ID aus der Map unter Verwendung der Zeilennummer
        let movie_id = movieIdMap.get(rowNumber);
        fetch(`http://127.0.0.1:5000/api/movies/${movie_id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.message) {
                alert('Film erfolgreich gelöscht');
                // Entfernen Sie die Zeile aus der Tabelle
                let row = document.getElementById(`movie-${rowNumber}`);
                row.parentNode.removeChild(row);
            } else {
                alert('Fehler: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Fehler:', error);
        });
    }

    function delete_serie(rowNumber) {
        // Holen Sie die Movie-ID aus der Map unter Verwendung der Zeilennummer
        let serie_id = serieIdMap.get(rowNumber);
        fetch(`http://127.0.0.1:5000/api/series/${serie_id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.message) {
                alert('Serie erfolgreich gelöscht');
                // Entfernen Sie die Zeile aus der Tabelle
                let row = document.getElementById(`serie-${rowNumber}`);
                row.parentNode.removeChild(row);
            } else {
                alert('Fehler: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Fehler:', error);
        });
    }
});
    </script>

    <footer>
        <p id="Authors">Authors: Mohammad Freej <br> Dario Kasumovic Carballeira <br> Mohammad Jalal Mobasher Goljani <br> Katharina Nolte</p>
        <p id="Mail"><a href="mailto:hege@example.com">dario.carballeira98@www.de</a></p>
    </footer>
</body>
</html>
