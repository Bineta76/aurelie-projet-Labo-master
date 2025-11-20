<?php
// Connexion à la base
$host = "localhost";
$user = "root";
$pass = "";
$db = "labo";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Connexion échouée: " . $conn->connect_error);

// Ajouter un patient
if (isset($_POST['ajouter'])) {
    $nom = $_POST['nom'];
    $region = $_POST['region'];
    $medecin = $_POST['medecin'];

    $stmt = $conn->prepare("INSERT INTO patients (nom, region, medecin) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nom, $region, $medecin);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php");
    exit;
}

// Supprimer un patient
if (isset($_GET['supprimer'])) {
    $id = $_GET['supprimer'];
    $stmt = $conn->prepare("DELETE FROM patients WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php");
    exit;
}

// Récupérer tous les patients
$result = $conn->query("SELECT * FROM patients");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Patients</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4 text-center">Gestion des Patients</h1>

    <!-- Tableau des patients -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">Liste des Patients</div>
        <div class="card-body p-0">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-dark">
                <tr>
                    <th>Nom</th>
                    <th>Région</th>
                    <th>Médecin</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['nom']) ?></td>
                        <td><?= htmlspecialchars($row['region']) ?></td>
                        <td><?= htmlspecialchars($row['medecin']) ?></td>
                        <td>
                            <a href="?supprimer=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
                               onclick="return confirm('Voulez-vous vraiment supprimer ce patient ?')">
                                Supprimer
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Formulaire d'ajout -->
    <div class="card">
        <div class="card-header bg-success text-white">Ajouter un Patient</div>
        <div class="card-body">
            <form method="post" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Nom</label>
                    <input type="text" name="nom" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Région</label>
                    <input type="text" name="region" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Médecin</label>
                    <input type="text" name="medecin" class="form-control" required>
                </div>
                <div class="col-12">
                    <button type="submit" name="ajouter" class="btn btn-success">Ajouter</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
