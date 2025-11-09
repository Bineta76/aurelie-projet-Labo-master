
<!DOCTYPE html>
session_start();
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="styles.css" rel="stylesheet">
    <title>Formulaire d'inscription</title>
</head>
<body>
    <div class="container">
        <h2>Formulaire d'inscription</h2>
        <form action="inscription.php" method="POST">
            <label for="username">Nom</label>
            <input type="text" id="username" name="username" required>

            <label for="Prenom">Prenom</label>
            <input type="text" id="Prenom" name="Prenom" required>

            <label for="Login">Login</label>
            <input type="text" id="Login" name="Login" required>


            <label for="email">Email</label>
            <input type="text" id="email" name="email" required>

            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required>

            <input type="submit" value="S'inscrire">
        </form>
    </div>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Connexion à la base de données
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "inscriptions";

        // Créer la connexion
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Vérifier la connexion
        if ($conn->connect_error) {
            die("Échec de la connexion : " . $conn->connect_error);
        }

        // Récupérer les données du formulaire
        $user = $_POST['username'];
        $email = $_POST['email'];
        $pass = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hachage du mot de passe

        // Insérer les données dans la base
        $sql = "INSERT INTO utilisateurs (nom_utilisateur, email, mot_de_passe) VALUES ('$user', '$email', '$pass')";

        if ($conn->query($sql) === TRUE) {
            echo "Inscription réussie !";
        } else {
            echo "Erreur : " . $sql . "<br>" . $conn->error;
        }

        // Fermer la connexion
        $conn->close();
    }
    ?>
</body>
</html>


