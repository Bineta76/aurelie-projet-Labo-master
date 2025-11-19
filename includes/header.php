<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health North</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/styles.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">Health North</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>" href="index.php">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'inscription.php' ? 'active' : '' ?>" href="inscription.php">Inscription</a></li>
                    <li class="nav-item"><a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'quiSommesNous.php' ? 'active' : '' ?>" href="quiSommesNous.php">Qui sommes-nous ?</a></li>
                    <li class="nav-item"><a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'rendez_vous.php' ? 'active' : '' ?>" href="rendez_vous.php">Créer un rendez-vous</a></li>
                    <li class="nav-item"><a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'bilan.php' ? 'active' : '' ?>" href="bilan.php">Bilan</a></li>
                    <li class="nav-item"><a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'organniser.php' ? 'active' : '' ?>" href="organiser.php">Organiser</a></li>     
                   t <li class="nav-item"><a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'contactSupport.php' ? 'active' : '' ?>" href="contactSupport.php">Aide</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
