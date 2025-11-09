<?php
include 'includes/header.php';
session_start();
?>
<?php
// ✅ Affichage des erreurs pour le développement
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ✅ Connexion à MySQL avec PDO
try {
    $pdo = new PDO('mysql:host=localhost;dbname=labo;charset=utf8', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// ✅ Requête : récupérer tous les rendez-vous avec jointures
$sql = "
    SELECT 
        r.date,
        m.prenom AS prenom_medecin,
        m.nom AS nom_medecin,
        e.nom AS nom_examen,
        c.nom_cabinet
    FROM rdv r
    JOIN medecin m ON m.id = r.id_medecin
    JOIN examen e ON e.id = r.id_examen
    JOIN cabinet_medical c ON c.id = r.id_cabinet_medical
    ORDER BY r.date DESC
";

// ✅ Exécution de la requête
try {
    $rendezvous = $pdo->query($sql)->fetchAll();
} catch (PDOException $e) {
    die("Erreur lors de la récupération des rendez-vous : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Rendez-vous</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container my-5">
    <h1 class="mb-4 text-center">📋 Liste des Rendez-vous</h1>

    <?php if (empty($rendezvous)): ?>
        <div class="alert alert-info text-center">
            Aucun rendez-vous trouvé dans la base de données.
        </div>
    <?php else: ?>
        <table class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th>Médecin</th>
                    <th>Examen</th>
                    <th>Cabinet médical</th>
                    <th>Date / Heure</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($rendezvous as $rdv): ?>
                <tr>
                    <td><?= htmlspecialchars($rdv['prenom_medecin'] . ' ' . $rdv['nom_medecin']) ?></td>
                    <td><?= htmlspecialchars($rdv['nom_examen']) ?></td>
                    <td><?= htmlspecialchars($rdv['nom_cabinet']) ?></td>
                    <td><?= date('d/m/Y à H\hi', strtotime($rdv['date'])) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</div>
</body>
</html>
