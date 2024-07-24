<!DOCTYPE html>
<head>
    <meta charset="UTF-8"
            lang="de"
            name="viewport" content="width=device-width, initial-scale=1" />
            <link rel="stylesheet" href="../www/css/style.css">    <title>SteamDB</title>
</head>
<body>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $vorname = $_POST['vorname'];
        $nachname = $_POST['nachname'];
        $benutzername = $_POST['benutzername'];
        $email = $_POST['email'];
        $passwort = $_POST['passwort'];
    }
     // Variablen für die Datenbankverbindung erstellen
     $servername = "localhost";
     $username = "root";
     $password = "";
     $dbname = "steamdb";
     
     // Verbindung zur Datenbank erstellen
     $conn = new mysqli($servername, $username, $password, $dbname);
     
     // Verbindung überprüfen
     if ($conn->connect_error) {
         die("Verbindung fehlgeschlagen: " . $conn->connect_error);
     }

     if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Neuen Datensatz eintragen
        $sql = "INSERT INTO usrs (email, nachname, vorname, benutzername, passwort) VALUES ('$email', '$nachname', '$vorname', '$benutzername', '$passwort')";
        if ($conn->query($sql) === TRUE) {
            header("Location: index1.php");
            echo "Neuer Datensatz erfolgreich erstellt.<br>";
        } else {
            echo "Fehler: " . $sql . "<br>" . $conn->error;
        }

        // $sql = "SELECT email, nachname, vorname, benutzername, passwort FROM usrs ORDER BY nachname DESC";
        //     $result = $conn->query($sql);
        
        //     if ($result->num_rows > 0) {
        //         echo "<h2>Alle Einträge:</h2>";
        //         while($row = $result->fetch_assoc()) {
        //             echo "email: " . $row["email"]. " - Nachname: " . $row["nachname"]. " - vorname: " . $row["vorname"]. " - benutzername: " . $row["benutzername"]. " - passwort: " . $row["passwort"]. "<br>";
        //         }
        //     } else {
        //         echo "0 Ergebnisse";
        //     }
         }
        
        $conn->close();
?>

    <form action="users.php" method="POST">
        email: <input type="text" name="email" required><br><br>
        nachname: <input type="text" name="nachname" required><br><br>
        vorname: <input type="text" name="vorname" required><br><br>
        benutzername: <input type="text" name="benutzername" required><br><br>
        passwort: <input type="text" name="passwort" required><br><br>
        <input type="submit" value="Submit">
    </form>
</body>