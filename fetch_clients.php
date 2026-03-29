<?php
// fetch_clients.php
header('Content-Type: application/json');

// Connexion à la base de données
$conn = new mysqli("localhost", "root", "", "amal");

// Vérifier la connexion
if ($conn->connect_error) {
    die(json_encode(["error" => "La connexion a échoué : " . $conn->connect_error]));
}

// Initialisation de la requête SQL
$sql = "SELECT c.id, c.nom, c.prenom, c.telephone, c.email 
        FROM clients c";

// Gestion des filtres
$conditions = [];

// Filtrage par session
if (!empty($_POST['session_id'])) {
    $session_id = intval($_POST['session_id']);
    $sql .= " INNER JOIN client_formations cf ON c.id = cf.client_id";
    $conditions[] = "cf.session_id = $session_id";
}

// Filtrage par lettre
if (!empty($_POST['letter'])) {
    $letter = $conn->real_escape_string($_POST['letter']);
    $conditions[] = "c.nom LIKE '$letter%'";
}

// Ajout des conditions à la requête
if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

// Tri par ordre alphabétique
$sql .= " ORDER BY c.nom ASC";

$result = $conn->query($sql);

$clients = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $clients[] = $row;
    }
}

// Fermeture de la connexion
$conn->close();

// Renvoi des données au format JSON
echo json_encode($clients);
?>
