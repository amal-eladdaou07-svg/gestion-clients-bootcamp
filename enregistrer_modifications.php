<?php
// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "amal";

$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("La connexion a échoué: " . $conn->connect_error);
}

// Récupérer les données du formulaire
$client_id = $_POST['client_id'];
$nom = $_POST['nom'];
$prenom = $_POST['prenom'];
$telephone = $_POST['telephone'];
$email = $_POST['email'];
$mode_paiement = $_POST['mode_paiement'];
$montant = $_POST['montant'];
$montant_restant = $_POST['montant_restant'];
$commentaire = $_POST['commentaire'];

// Mettre à jour les informations du client
$sql = "UPDATE clients 
        SET nom = ?, prenom = ?, telephone = ?, email = ?, mode_paiement = ?, montant = ?, montant_restant = ?, commentaire = ? 
        WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssdsi", $nom, $prenom, $telephone, $email, $mode_paiement, $montant, $montant_restant, $commentaire, $client_id);

if ($stmt->execute()) {
    $success = true;
} else {
    $success = false;
}
$stmt->close();

// Gérer les formations associées
$sql = "DELETE FROM client_formations WHERE client_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $client_id);
$stmt->execute();
$stmt->close();

if (isset($_POST['formation']) && is_array($_POST['formation'])) {
    $sql_formation = "SELECT id FROM formations WHERE nom = ?";
    $sql_insert_formation = "INSERT INTO formations (nom) VALUES (?)";
    $sql_session = "SELECT id FROM sessions WHERE formation_id = ? AND session_date = ?";
    $sql_insert_session = "INSERT INTO sessions (formation_id, session_date, date_fin) VALUES (?, ?, ?)";
    $sql_insert_client_formation = "INSERT INTO client_formations (client_id, session_id) VALUES (?, ?)";

    foreach ($_POST['formation'] as $index => $formation) {
        $stmt = $conn->prepare($sql_formation);
        $stmt->bind_param("s", $formation);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($formation_id);
            $stmt->fetch();
        } else {
            $stmt->close();
            $stmt = $conn->prepare($sql_insert_formation);
            $stmt->bind_param("s", $formation);
            $stmt->execute();
            $formation_id = $stmt->insert_id;
        }
        $stmt->close();

        $session_date = $_POST['session'][$index];
        $date_fin = $_POST['date_fin'][$index];
        $stmt = $conn->prepare($sql_session);
        $stmt->bind_param("is", $formation_id, $session_date);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($session_id);
            $stmt->fetch();
        } else {
            $stmt->close();
            $stmt = $conn->prepare($sql_insert_session);
            $stmt->bind_param("iss", $formation_id, $session_date, $date_fin);
            $stmt->execute();
            $session_id = $stmt->insert_id;
        }
        $stmt->close();

        $stmt = $conn->prepare($sql_insert_client_formation);
        $stmt->bind_param("ii", $client_id, $session_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Gérer les attestations
if (isset($_FILES['attestations'])) {
    $uploadDir = 'uploads/';
    foreach ($_FILES['attestations']['tmp_name'] as $index => $tmpName) {
        if ($_FILES['attestations']['error'][$index] === UPLOAD_ERR_OK) {
            $filename = basename($_FILES['attestations']['name'][$index]);
            $filePath = $uploadDir . $filename;
            if (move_uploaded_file($tmpName, $filePath)) {
                $sql = "INSERT INTO attestations (client_id, fichier) VALUES (?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("is", $client_id, $filename);
                $stmt->execute();
                $stmt->close();
            }
        }
    }
}

// Redirection vers la page de modification avec un message de succès
$redirect_url = "modifier.php?id=$client_id&message=";
$redirect_url .= $success ? "success" : "error";
$redirect_url .= "#message";
header("Location: $redirect_url");

$conn->close();
?>
