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

// Ajout d’un patient
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $region = trim($_POST['region'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($nom && $prenom && $email) {
        // Vérifier si l'email existe déjà
        $stmt = $pdo->prepare("SELECT id FROM patient WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            echo "<div class='alert alert-warning'>⚠️ Cet email existe déjà.</div>";
        } else {
            try {
                // Insérer le patient avec la région
                $stmt = $pdo->prepare("INSERT INTO patient (nom, prenom, region, email) VALUES (?, ?, ?, ?)");
                $stmt->execute([$nom, $prenom, $region, $email]);
                header("Location: patients.php");
                exit;
            } catch (PDOException $e) {
                echo "<div class='alert alert-danger'>❌ Erreur PDO : " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        }
    } else {
        echo "<div class='alert alert-warning'>⚠️ Nom, prénom et email sont obligatoires.</div>";
    }
}

// Suppression d’un patient
if (isset($_GET['supprimer'])) {
    $id = intval($_GET['supprimer']);
    $stmt = $pdo->prepare("DELETE FROM patient WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: patients.php");
    exit;
}

// Récupération des patients
try {
    $stmt = $pdo->query("SELECT id, nom, prenom, region, email FROM patient ORDER BY id ASC");
    $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("❌ Erreur PDO : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Patients</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h1 class="mb-4">👨‍⚕️ Gestion des Patients</h1>

    <!-- Formulaire d'ajout -->
    <div class="card mb-4">
        <div class="card-header">Ajouter un nouveau patient</div>
        <div class="card-body">
            <form method="post" action="">
                <div class="mb-3">
                    <input type="text" name="nom" class="form-control" placeholder="Nom" required>
                </div>
                <div class="mb-3">
                    <input type="text" name="prenom" class="form-control" placeholder="Prénom" required>
                </div>
                <div class="mb-3">
                    <input type="text" name="region" class="form-control" placeholder="Région">
                </div>
                <div class="mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                </div>
                <button type="submit" class="btn btn-primary">Ajouter</button>
            </form>
        </div>
    </div>

    <!-- Tableau des patients -->
    <?php if (!empty($patients)): ?>
        <table class="table table-bordered table-striped bg-white">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Région</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($patients as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['id']) ?></td>
                        <td><?= htmlspecialchars($p['nom']) ?></td>
                        <td><?= htmlspecialchars($p['prenom']) ?></td>
                        <td><?= htmlspecialchars($p['region'] ?: 'Non renseigné') ?></td>
                        <td><?= htmlspecialchars($p['email']) ?></td>
                        <td>
                            <a href="?supprimer=<?= urlencode($p['id']) ?>" 
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Supprimer ce patient ?');">
                               Supprimer
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="text-muted">Aucun patient enregistré.</p>
    <?php endif; ?>
</div>
</body>
</html>
