<?php
header("Access-Control-Allow-Origin: *");
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
   API CRUD pour les rendez-vous
   ============================== */

// 🔹 GET => Lire les rendez-vous
if ($method === 'GET') {
    if (isset($_GET['id'])) {
        $stmt = $pdo->prepare("SELECT rv.*, m.nom AS nom_medecin, m.specialite 
                               FROM rendez_vous rv 
                               LEFT JOIN medecins m ON rv.medecin_id = m.id
                               WHERE rv.id = ?");
        $stmt->execute([intval($_GET['id'])]);
        $rdv = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($rdv ?: ["message" => "Rendez-vous non trouvé"]);
    } else {
        $stmt = $pdo->query("SELECT rv.*, m.nom AS nom_medecin, m.specialite 
                             FROM rendez_vous rv 
                             LEFT JOIN medecins m ON rv.medecin_id = m.id
                             ORDER BY rv.date_rdv DESC, rv.heure_rdv DESC");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
}

/* -------------------------------------------------- */

// 🔹 POST => Ajouter un rendez-vous
elseif ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!empty($data['nom_patient']) && !empty($data['date_rdv']) && !empty($data['heure_rdv'])) {
        $stmt = $pdo->prepare("INSERT INTO rendez_vous 
            (nom_patient, email_patient, telephone, date_rdv, heure_rdv, medecin_id, commentaire)
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['nom_patient'],
            $data['email_patient'] ?? '',
            $data['telephone'] ?? '',
            $data['date_rdv'],
            $data['heure_rdv'],
            $data['medecin_id'] ?? null,
            $data['commentaire'] ?? ''
        ]);
        echo json_encode(["message" => "✅ Rendez-vous ajouté avec succès."]);
    } else {
        http_response_code(400);
        echo json_encode(["error" => "Le nom du patient, la date et l'heure sont obligatoires."]);
    }
}

/* -------------------------------------------------- */

// 🔹 PUT => Modifier un rendez-vous
elseif ($method === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!empty($data['id'])) {
        $stmt = $pdo->prepare("UPDATE rendez_vous 
            SET nom_patient=?, email_patient=?, telephone=?, date_rdv=?, heure_rdv=?, medecin_id=?, commentaire=?
            WHERE id=?");
        $stmt->execute([
            $data['nom_patient'],
            $data['email_patient'] ?? '',
            $data['telephone'] ?? '',
            $data['date_rdv'],
            $data['heure_rdv'],
            $data['medecin_id'] ?? null,
            $data['commentaire'] ?? '',
            intval($data['id'])
        ]);
        echo json_encode(["message" => "✅ Rendez-vous mis à jour avec succès."]);
    } else {
        http_response_code(400);
        echo json_encode(["error" => "L'ID du rendez-vous est requis pour la mise à jour."]);
    }
}

/* -------------------------------------------------- */

// 🔹 DELETE => Supprimer un rendez-vous
elseif ($method === 'DELETE') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!empty($data['id'])) {
        $stmt = $pdo->prepare("DELETE FROM rendez_vous WHERE id = ?");
        $stmt->execute([intval($data['id'])]);
        echo json_encode(["message" => "🗑️ Rendez-vous supprimé avec succès."]);
    } else {
        http_response_code(400);
        echo json_encode(["error" => "L'ID du rendez-vous est requis pour la suppression."]);
    }
}

/* -------------------------------------------------- */

// 🔹 OPTIONS => Requête préliminaire CORS
elseif ($method === 'OPTIONS') {
    http_response_code(200);
}

/* -------------------------------------------------- */

else {
    http_response_code(405);
    echo json_encode(["error" => "Méthode non autorisée."]);
}
