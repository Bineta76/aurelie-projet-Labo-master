<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$method = $_SERVER['REQUEST_METHOD'];

// ✅ Réponse immédiate pour les requêtes OPTIONS (CORS)
if ($method == 'OPTIONS') {
    http_response_code(200);
    exit;
}

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
    echo json_encode(['message' => 'Erreur de connexion à la base de données: ' . $e->getMessage()]);
    exit;
}

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// ✅ Route : liste de tous les spécialistes
if ($path == '/aurelie-projet-labo/api/api-specialiste.php/specialistes' && $method == 'GET') {
    try {
        $search = isset($_GET['search']) ? $_GET['search'] : '';

        if (!empty($search)) {
            $stmt = $pdo->prepare("SELECT * FROM specialiste WHERE nom LIKE ? OR prenom LIKE ? OR specialite LIKE ? OR ville LIKE ?");
            $searchParam = "%$search%";
            $stmt->execute([$searchParam, $searchParam, $searchParam, $searchParam]);
        } else {
            $stmt = $pdo->query("SELECT * FROM specialiste");
        }

        $specialistes = $stmt->fetchAll();
        echo json_encode($specialistes);
        exit;
    } catch (\Exception $e) {
        echo json_encode(['message' => 'Erreur lors de la récupération des spécialistes: ' . $e->getMessage()]);
        exit;
    }
}
// ✅ Route : un spécialiste spécifique
elseif (preg_match('#^/aurelie-projet-labo/api/api-specialiste.php/specialistes/(\d+)$#', $path, $matches) && $method == 'GET') {
    try {
        $id = $matches[1];
        $stmt = $pdo->prepare("SELECT * FROM specialiste WHERE id = ?");
        $stmt->execute([$id]);
        $specialiste = $stmt->fetch();

        if ($specialiste) {
            echo json_encode($specialiste);
        } else {
            echo json_encode(['message' => 'Spécialiste non trouvé']);
        }

        exit;
    } catch (\Exception $e) {
        echo json_encode(['message' => 'Erreur lors de la récupération du spécialiste: ' . $e->getMessage()]);
        exit;
    }
}
