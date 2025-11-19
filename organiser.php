<?php
session_start();

// Connexion à la base de données
try {
    $pdo = new PDO("mysql:host=localhost;dbname=labo;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Erreur de connexion : " . $e->getMessage());
}

// -------------------- AJOUT DE PATIENT --------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'ajouter') {
        $nom = trim($_POST['nom'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        $region = trim($_POST['region'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if ($nom && $prenom && $email) {
            // Vérifier si email existe déjà
            $stmt = $pdo->prepare("SELECT id FROM patient WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $message = "<div class='alert alert-warning'>⚠️ Cet email existe déjà.</div>";
            } else {
                $stmt = $pdo->prepare("INSERT INTO patient (nom, prenom, region, email) VALUES (?, ?, ?, ?)");
                $stmt->execute([$nom, $prenom, $region, $email]);
                header("Location: patients.php");
                exit;
            }
        } else {
            $message = "<div class='alert alert-warning'>⚠️ Nom, prénom et email sont obligatoires.</div>";
        }
    }

    // -------------------- MODIFICATION DE PATIENT --------------------
    if ($action === 'modifier') {
        $id = intval($_POST['id']);
        $nom = trim($_POST['nom'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        $region = trim($_POST['region'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if ($nom && $prenom && $email) {
            $stmt = $pdo->prepare("UPDATE patient SET nom = ?, prenom = ?, region = ?, email = ? WHERE id = ?");
            $stmt->execute([$nom, $prenom, $region, $email, $id]);
            header("Location: patients.php");
            exit;
        } else {
            $message = "<div class='alert alert-warning'>⚠️ Tous les champs sont obligatoires pour la mise à jour.</div>";
        }
    }
}

// -------------------- SUPPRESSION DE PATIENT --------------------
if (isset($_GET['supprimer'])) {
    $id = intval($_GET['supprimer']);
    $stmt = $pdo->prepare("DELETE FROM patient WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: patients.php");
    exit;
}

// -------------------- RÉCUPÉRATION DES PATIENTS --------------------
$stmt = $pdo->query("SELECT id, nom, prenom, region, email FROM patient ORDER BY id ASC");
$patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    <!-- Message d'alerte -->
    <?php if (!empty($message)) echo $message; ?>

    <!-- Formulaire d'ajout -->
    <div class="card mb-4">
        <div class="card-header">Ajouter un nouveau patient</div>
        <div class="card-body">
            <form method="post" action="">
                <input type="hidden" name="action" value="ajouter">
                <div class="row g-3">
                    <div class="col-md-3">
                        <input type="text" name="nom" class="form-control" placeholder="Nom" required>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="prenom" class="form-control" placeholder="Prénom" required>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="region" class="form-control" placeholder="Région">
                    </div>
                    <div class="col-md-3">
                        <input type="email" name="email" class="form-control" placeholder="Email" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Ajouter</button>
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
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($patients as $p): ?>
                    <tr>
                        <form method="post" action="">
                            <td><?= htmlspecialchars($p['id']) ?>
                                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                            </td>
                            <td><input type="text" name="nom" value="<?= htmlspecialchars($p['nom']) ?>" class="form-control" required></td>
                            <td><input type="text" name="prenom" value="<?= htmlspecialchars($p['prenom']) ?>" class="form-control" required></td>
                            <td><input type="text" name="region" value="<?= htmlspecialchars($p['region']) ?>" class="form-control"></td>
                            <td><input type="email" name="email" value="<?= htmlspecialchars($p['email']) ?>" class="form-control" required></td>
                            <td>
                                <button type="submit" name="action" value="modifier" class="btn btn-success btn-sm">💾 Sauvegarder</button>
                                <a href="?supprimer=<?= urlencode($p['id']) ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Supprimer ce patient ?');">
                                   Supprimer
                                </a>
                            </td>
                        </form>
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
