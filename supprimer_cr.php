<?php
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID du compte rendu invalide.");
}

$id = (int) $_GET['id'];

// Connexion PDO
$pdo = new PDO("mysql:host=localhost;dbname=labo;charset=utf8", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

// Suppression
$stmt = $pdo->prepare("DELETE FROM compte_rendu WHERE id = ?");
$stmt->execute([$id]);

header("Location: liste_cr.php");
exit;
