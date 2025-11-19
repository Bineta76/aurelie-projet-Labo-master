<?php
include 'includes/db.php';
session_start();

// Traitement du formulaire
$message = "";

try {
    $pdo = new PDO("mysql:host=localhost;dbname=labo;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        if (!empty($_POST['nom']) && !empty($_POST['prenom']) &&
            !empty($_POST['id_medecin']) && !empty($_POST['date_rdv']) && !empty($_POST['heure'])) {

            $stmt = $pdo->prepare("
                INSERT INTO rendez_vous(nom, prenom, id_medecin, date_rdv, heure)
                VALUES (?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $_POST['nom'],
                $_POST['prenom'],
                $_POST['id_medecin'],
                $_POST['date_rdv'],
                $_POST['heure']
            ]);

            $message = "<div class='alert alert-success'>Rendez-vous créé avec succès !</div>";
        } else {
            $message = "<div class='alert alert-danger'>Erreur : tous les champs sont obligatoires.</div>";
        }
    }

} catch (PDOException $e) {
    $message = "<div class='alert alert-danger'>Erreur : " . $e->getMessage() . "</div>";
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un rendez-vous</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">

    <div class="card shadow p-4">
        <h2 class="text-center mb-4">Créer un Rendez-vous</h2>

        <?= $message ?>

        <form action="" method="post">

            <div class="mb-3">
                <label class="form-label">Nom :</label>
                <input type="text" name="nom" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Prénom :</label>
                <input type="text" name="prenom" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Médecin :</label>
                <select name="id_medecin" class="form-select" required>
                    <option value="">-- Choisir --</option>
                    <option value="1">Dr Martin</option>
                    <option value="2">Dr Dupont</option>
                    <option value="3">Dr Laurent</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Date :</label>
                <input type="date" name="date_rdv" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Heure :</label>
                <input type="time" name="heure" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Créer le rendez-vous</button>

        </form>
    </div>

</div>

</body>
</html>
