<?php
include 'includes/db.php';

session_start();

header('Content-Type: application/json; charset=utf-8');

try {
    $stmt = $pdo->query("
        SELECT r.id, p.nom AS patient_nom, m.nom AS medecin_nom, r.date, r.heure
        FROM rendez_vous r
        JOIN patient p ON r.id_patient = p.id
        JOIN medecins m ON r.id_medecin = m.id
        ORDER BY r.date, r.heure
    ");

    $first = true; // pour gérer la virgule JSON
    echo "["; // ouverture d'un tableau JSON

    while ($rdv = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (!empty($rdv['date']) && !empty($rdv['heure'])) {
            $start = (new DateTime($rdv['date'] . ' ' . $rdv['heure']))->format('c');
            $end = (new DateTime($rdv['date'] . ' ' . $rdv['heure']))->add(new DateInterval('PT30M'))->format('c');

            if (!$first) echo ",";
            echo json_encode([
                'id'    => $rdv['id'],
                'title' => "{$rdv['patient_nom']} - {$rdv['medecin_nom']}",
                'start' => $start,
                'end'   => $end
            ], JSON_UNESCAPED_UNICODE);
            $first = false;
        }
    }

    echo "]"; // fermeture du tableau JSON

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => "Erreur : " . $e->getMessage()]);
}
