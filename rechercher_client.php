<?php
// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "amal";

// Créer une connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Vérifier si les données nécessaires sont présentes
if (isset($_POST['mod_nom']) && isset($_POST['mod_prenom']) && isset($_POST['mod_telephone'])) {
    $nom = $_POST['mod_nom'];
    $prenom = $_POST['mod_prenom'];
    $telephone = $_POST['mod_telephone'];

    // Préparation de la requête pour trouver le client
    $stmt = $conn->prepare("SELECT * FROM clients WHERE nom = ? AND prenom = ? AND telephone = ?");
    $stmt->bind_param("sss", $nom, $prenom, $telephone);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $client = $result->fetch_assoc();

        // Rechercher les formations et sessions associées au client
        $stmt2 = $conn->prepare("
            SELECT f.nom AS formation, s.session_date, s.date_fin
            FROM client_formations cf 
            JOIN sessions s ON cf.session_id = s.id 
            JOIN formations f ON s.formation_id = f.id 
            WHERE cf.client_id = ?
        ");
        $stmt2->bind_param("i", $client['id']);
        $stmt2->execute();
        $result2 = $stmt2->get_result();
        $formations = $result2->fetch_all(MYSQLI_ASSOC);

        // Ajouter les données de formations au client
        $client['formations'] = $formations;

        // Rechercher les attestations associées au client
        $stmt3 = $conn->prepare("SELECT fichier FROM attestations WHERE client_id = ?");
        $stmt3->bind_param("i", $client['id']);
        $stmt3->execute();
        $result3 = $stmt3->get_result();
        $attestations = $result3->fetch_all(MYSQLI_ASSOC);

        // Ajouter les données d'attestations au client
        $client['attestations'] = $attestations;

        echo json_encode($client);
    } else {
        echo json_encode(['error' => 'Client non trouvé !']);
    }

    $stmt->close();
    $stmt2->close();
    $stmt3->close();
} else {
    echo json_encode(['error' => 'Données non valides !']);
}

$conn->close();
?>
