<?php
header('Content-Type: application/json');

// Informations de connexion à la base de données
$servername = "localhost";
$username = "root";
$password = ""; // Mettez votre mot de passe ici
$dbname = "amal";

// Création de la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Requête pour obtenir les statistiques des formations
$sql = "
    SELECT f.nom AS formation, COUNT(cf.id) AS count
    FROM client_formations cf
    JOIN sessions s ON cf.session_id = s.id
    JOIN formations f ON s.formation_id = f.id
    GROUP BY f.nom
";
$result = $conn->query($sql);

$data = [];

if ($result->num_rows > 0) {
    // Collecter les données
    while($row = $result->fetch_assoc()) {
        $data[] = [
            'formation' => $row['formation'],  // Utilisez 'formation' comme clé
            'count' => (int)$row['count']
        ];
    }
} else {
    error_log("Aucun résultat trouvé dans la requête SQL.");
}

// Fermer la connexion
$conn->close();

// Envoyer les données au format JSON
echo json_encode($data);
?>
