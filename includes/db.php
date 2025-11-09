<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "labo";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    // echo "Connexion PDO réussie !";
} catch (PDOException $e) {
    die("Erreur de connexion PDO : " . $e->getMessage());
}
?>
