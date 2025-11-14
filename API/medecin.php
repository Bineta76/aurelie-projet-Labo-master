<?php
header("Access-Control-Allow-Origin: *"); // Permet d'accéder à l'API depuis Flutter / JS
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$method = $_SERVER['REQUEST_METHOD'];

// Connexion à la base de données
try {
    $pdo = new PDO("mysql:host=localhost;dbname=labo;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erreur de connexion : " . $e->getMessage()]);
    exit;
}

/* ==============================
   ROUTES CRUD
   ============================== */

// 🔹 GET => Récupérer tous les médecins ou un seul par ID
if ($method === 'GET') {
    if (isset($_GET['id'])) {
        $stmt = $pdo->prepare("SELECT * FROM medecins WHERE id = ?");
        $stmt->execute([intval($_GET['id'])]);
        $medecin = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($medecin ?: ["message" => "Médecin non trouvé"]);
    } else {
        $stmt = $pdo->query("SELECT * FROM medecins ORDER BY id DESC");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
}

/* -------------------------------------------------- */

// 🔹 POST => Ajouter un nouveau médecin
elseif ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!empty($data['nom']) && !empty($data['specialite'])) {
        $stmt = $pdo->prepare("INSERT INTO medecins (nom, specialite, telephone, email) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $data['nom'],
            $data['specialite'],
            $data['telephone'] ?? '',
            $data['email'] ?? ''
        ]);
        echo json_encode(["message" => "Médecin ajouté avec succès ✅"]);
    } else {
        http_response_code(400);
        echo json_encode(["error" => "Le nom et la spécialité sont obligatoires."]);
    }
}

/* -------------------------------------------------- */

// 🔹 PUT => Modifier un médecin existant
elseif ($method === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!empty($data['id']) && !empty($data['nom']) && !empty($data['specialite'])) {
        $stmt = $pdo->prepare("UPDATE medecins SET nom=?, specialite=?, telephone=?, email=? WHERE id=?");
        $stmt->execute([
            $data['nom'],
            $data['specialite'],
            $data['telephone'] ?? '',
            $data['email'] ?? '',
            intval($data['id'])
        ]);
        echo json_encode(["message" => "Médecin mis à jour avec succès ✅"]);
    } else {
        http_response_code(400);
        echo json_encode(["error" => "ID, nom et spécialité requis pour la mise à jour."]);
    }
}

/* -------------------------------------------------- */

// 🔹 DELETE => Supprimer un médecin
elseif ($method === 'DELETE') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!empty($data['id'])) {
        $stmt = $pdo->prepare("DELETE FROM medecins WHERE id = ?");
        $stmt->execute([intval($data['id'])]);
        echo json_encode(["message" => "Médecin supprimé avec succès 🗑️"]);
    } else {
        http_response_code(400);
        echo json_encode(["error" => "ID du médecin requis pour la suppression."]);
    }
}

/* -------------------------------------------------- */

// 🔹 OPTIONS => Prévol CORS
elseif ($method === 'OPTIONS') {
    http_response_code(200);
}

else {
    http_response_code(405);
    echo json_encode(["error" => "Méthode non autorisée"]);
}
