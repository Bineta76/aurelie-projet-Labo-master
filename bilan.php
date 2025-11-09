<?php
session_start();
include 'includes/header.php';

// Connexion à la base de données
try {
    $pdo = new PDO("mysql:host=localhost;dbname=labo;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Erreur de connexion : " . $e->getMessage());
}

// Suppression d’un compte rendu
if (isset($_GET['supprimer'])) {
    $id = intval($_GET['supprimer']);
    $stmt = $pdo->prepare("DELETE FROM bilan WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: bilan.php");
    exit;
}

// Ajout d’un compte rendu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['contenu'])) {
    $contenu = trim($_POST['contenu']);
    if ($contenu !== '') {
        try {
            $stmt = $pdo->prepare("INSERT INTO bilan (contenu) VALUES (?)");
            $stmt->execute([$contenu]);
            header("Location: bilan.php");
            exit;
        } catch (PDOException $e) {
            echo "<div class='alert alert-danger'>❌ Erreur PDO : " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    } else {
        echo "<div class='alert alert-warning'>⚠️ Le contenu est vide.</div>";
    }
}

// Récupération des comptes rendus
try {
    $stmt = $pdo->query("SELECT id, contenu, date_creation FROM bilan ORDER BY id DESC");
    $bilanListe = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("❌ Erreur PDO : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Compte Rendu Médical</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h1 class="mb-4">🩺 Gestion des comptes rendus</h1>

    <!-- Formulaire d'ajout -->
    <div class="card mb-4">
        <div class="card-header">Ajouter un nouveau compte rendu</div>
        <div class="card-body">
            <form method="post" action="">
                <div class="mb-3">
                    <textarea name="contenu" class="form-control" rows="4" placeholder="Écris ton compte rendu ici..." required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Ajouter</button>
            </form>
        </div>
    </div>

    <!-- Liste des comptes rendus -->
    <?php if (!empty($bilanListe)): ?>
        <?php foreach ($bilanListe as $row): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">📝 Compte rendu #<?= htmlspecialchars($row['id']) ?></h5>
                    <p class="card-text"><?= nl2br(htmlspecialchars($row['contenu'])) ?></p>
                    <small class="text-muted">Créé le : <?= htmlspecialchars($row['date_creation']) ?></small><br><br>
                    <a href="?supprimer=<?= urlencode($row['id']) ?>" 
                       class="btn btn-danger" 
                       onclick="return confirm('Supprimer ce compte rendu ?');">
                       Supprimer
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-muted">Aucun compte rendu disponible.</p>
    <?php endif; ?>
</div>
</body>
</html>
