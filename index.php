<?php
session_start();
include 'includes/header.php';

try {
    $pdo = new PDO('mysql:host=localhost;dbname=labo;charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Déconnexion
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header("Location: index.php");
    exit;
}

$message = '';
$mode = ($_GET['action'] ?? '') === 'inscription' ? 'inscription' : 'connexion';

// Formulaires
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['inscription'])) {
        $nom = trim($_POST['nom'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $mdp = $_POST['mot_de_passe'] ?? '';

        if ($nom === '' || $email === '' || $mdp === '') {
            $message = "❌ Tous les champs sont obligatoires.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "❌ Email invalide.";
        } else {
            $stmt = $pdo->prepare("SELECT id FROM patient WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $message = "❌ Cet email est déjà enregistré.";
            } else {
                $hash = password_hash($mdp, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO patient (nom, email, mot_de_passe) VALUES (?, ?, ?)");
                $stmt->execute([$nom, $email, $hash]);
                $message = "✅ Inscription réussie. Vous pouvez maintenant vous connecter.";
                $mode = 'connexion';
            }
        }
    }

    if (isset($_POST['connexion'])) {
        $email = trim($_POST['email'] ?? '');
        $mdp = $_POST['mot_de_passe'] ?? '';

        $stmt = $pdo->prepare("SELECT id, nom, mot_de_passe FROM patient WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($mdp, $user['mot_de_passe'])) {
            session_regenerate_id(true);
            $_SESSION['id_patient'] = $user['id'];
            $_SESSION['utilisateur'] = $user['nom'];
            header("Location: index.php");
            exit;
        } else {
            $message = "❌ Email ou mot de passe incorrect.";
        }
    }
}
?>

<h1 class="text-center my-4">Health North</h1>
<img src="image_labo.jpg" alt="Logo du laboratoire" class="d-block mx-auto mb-4" style="max-width:200px;">

<?php if (isset($_SESSION['id_patient'])): ?>
    <div class="text-center">
        <h2>Bienvenue, <?= htmlspecialchars($_SESSION['utilisateur']) ?> 👋</h2>
        <a href="?action=logout" class="btn btn-danger mt-3">Se déconnecter</a>
    </div>
<?php else: ?>
    <div class="card mx-auto" style="max-width:400px;">
        <div class="card-body">
            <h3 class="card-title text-center"><?= ($mode === 'connexion') ? 'Connexion' : 'Inscription' ?></h3>
            <?php if ($message): ?>
                <p class="text-danger text-center"><?= $message ?></p>
            <?php endif; ?>

            <form method="post">
                <?php if ($mode === 'inscription'): ?>
                    <div class="mb-3">
                        <label class="form-label">Nom :</label>
                        <input type="text" name="nom" class="form-control" required>
                    </div>
                <?php endif; ?>
                <div class="mb-3">
                    <label class="form-label">Email :</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Mot de passe :</label>
                    <input type="password" name="mot_de_passe" class="form-control" required>
                </div>
                <button type="submit" name="<?= $mode ?>" class="btn btn-primary w-100">
                    <?= ($mode === 'connexion') ? 'Se connecter' : "S'inscrire" ?>
                </button>
            </form>

            <p class="mt-3 text-center">
                <?= ($mode === 'connexion') 
                    ? "Pas encore de compte ? <a href='?action=inscription'>Inscrivez-vous</a>" 
                    : "Déjà inscrit ? <a href='?action=connexion'>Connectez-vous</a>" ?>
            </p>
        </div>
    </div>
<?php endif; ?>

</div> <!-- container ouvert dans header -->
</body>
</html>
