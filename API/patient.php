<?php
header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

$host = 'localhost';
$db   = 'labo';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    echo json_encode(['message' => 'Erreur de connexion : ' . $e->getMessage()]);
    exit;
}

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// ✅ Création d’un patient
if ($path == '/aurelie-projet-labo/api/api-patient.php/register' && $method == 'POST') {
    try {
        $nom = $input['nom'];
        $prenom = $input['prenom'];
        $email = $input['email'];
        $mdp = password_hash($input['mdp'], PASSWORD_DEFAULT);
        $adresse = $input['adresse'];

        $stmt = $pdo->prepare("INSERT INTO patient (nom, prenom, email, mdp, adresse) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nom, $prenom, $email, $mdp, $adresse]);
        echo json_encode(['message' => 'Patient créé avec succès']);
    } catch (\Exception $e) {
        echo json_encode(['message' => 'Erreur : ' . $e->getMessage()]);
    }
}

// ✅ Connexion d’un patient
elseif ($path == '/aurelie-projet-labo/api/api-patient.php/login' && $method == 'POST') {
    try {
        $email = $input['email'];
        $mdp = $input['mdp'];

        $stmt = $pdo->prepare("SELECT * FROM patient WHERE email = ?");
        $stmt->execute([$email]);
        $patient = $stmt->fetch();

        if ($patient && password_verify($mdp, $patient['mdp'])) {
            echo json_encode(['message' => 'Connexion réussie', 'patient' => $patient]);
        } else {
            echo json_encode(['message' => 'Identifiants incorrects']);
        }
    } catch (\Exception $e) {
        echo json_encode(['message' => 'Erreur : ' . $e->getMessage()]);
    }
}

// ✅ Liste de tous les patients
elseif ($path == '/aurelie-projet-labo/api/api-patient.php/patients' && $method == 'GET') {
    try {
        $stmt = $pdo->query("SELECT * FROM patient");
        echo json_encode($stmt->fetchAll());
    } catch (\Exception $e) {
        echo json_encode(['message' => 'Erreur : ' . $e->getMessage()]);
    }
}

// ✅ Détails d’un patient
elseif (preg_match('#^/aurelie-projet-labo/api/api-patient.php/patient/(\d+)$#', $path, $matches) && $method == 'GET') {
    try {
        $id = $matches[1];
        $stmt = $pdo->prepare("SELECT * FROM patient WHERE id = ?");
        $stmt->execute([$id]);
        $patient = $stmt->fetch();

        if ($patient) {
            echo json_encode($patient);
        } else {
            echo json_encode(['message' => 'Patient non trouvé']);
        }
    } catch (\Exception $e) {
        echo json_encode(['message' => 'Erreur : ' . $e->getMessage()]);
    }
}

// ✅ Mise à jour d’un patient
elseif (preg_match('#^/aurelie-projet-labo/api/api-patient.php/patient/(\d+)$#', $path, $matches) && $method == 'PUT') {
    try {
        $id = $matches[1];
        $nom = $input['nom'];
        $prenom = $input['prenom'];
        $email = $input['email'];
        $adresse = $input['adresse'];
        $mdp = isset($input['mdp']) ? password_hash($input['mdp'], PASSWORD_DEFAULT) : null;

        if ($mdp) {
            $stmt = $pdo->prepare("UPDATE patient SET nom=?, prenom=?, email=?, mdp=?, adresse=? WHERE id=?");
            $stmt->execute([$nom, $prenom, $email, $mdp, $adresse, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE patient SET nom=?, prenom=?, email=?, adresse=? WHERE id=?");
            $stmt->execute([$nom, $prenom, $email, $adresse, $id]);
        }

        echo json_encode(['message' => 'Patient mis à jour avec succès']);
    } catch (\Exception $e) {
        echo json_encode(['message' => 'Erreur : ' . $e->getMessage()]);
    }
}

// ✅ Suppression d’un patient
elseif (preg_match('#^/aurelie-projet-labo/api/api-patient.php/patient/(\d+)$#', $path, $matches) && $method == 'DELETE') {
    try {
        $id = $matches[1];
        $stmt = $pdo->prepare("DELETE FROM patient WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['message' => 'Patient supprimé avec succès']);
    } catch (\Exception $e) {
        echo json_encode(['message' => 'Erreur : ' . $e->getMessage()]);
    }
}

// 🚫 Route non trouvée
else {
    echo json_encode(['message' => 'Route non trouvée']);
}
?>
