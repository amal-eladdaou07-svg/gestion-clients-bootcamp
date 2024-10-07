<?php
// fetch_clients.php
header('Content-Type: application/json');

// Connexion à la base de données
$conn = new mysqli("localhost", "root", "", "amal");

// Vérifier la connexion
if ($conn->connect_error) {
    die("La connexion a échoué : " . $conn->connect_error);
}

// Filtrage par session si une session est sélectionnée
$session_filter = "";
if (isset($_POST['session_id']) && $_POST['session_id'] !== "") {
    $session_id = intval($_POST['session_id']);
    $session_filter = "INNER JOIN client_formations cf ON c.id = cf.client_id WHERE cf.session_id = $session_id";
}

// Requête SQL pour récupérer les clients
$sql = "SELECT c.id, c.nom, c.prenom, c.telephone, c.email 
        FROM clients c 
        $session_filter
        ORDER BY c.nom ASC";
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
