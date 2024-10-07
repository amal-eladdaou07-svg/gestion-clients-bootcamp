<?php
// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "amal";  // Nom de votre base de données

$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

// Obtenir l'ID du client à supprimer
$client_id = $_GET['id']; // L'ID du client est passé dans l'URL, ex: supprimer_client.php?id=1

// Démarrer la transaction
$conn->begin_transaction();

try {
    // Étape 1: Supprimer les attestations associées à ce client
    $delete_attestations = $conn->prepare("DELETE FROM attestations WHERE client_id = ?");
    $delete_attestations->bind_param("i", $client_id);
    $delete_attestations->execute();
    $delete_attestations->close();

    // Étape 2: Supprimer les formations associées à ce client
    $delete_formations = $conn->prepare("DELETE FROM client_formations WHERE client_id = ?");
    $delete_formations->bind_param("i", $client_id);
    $delete_formations->execute();
    $delete_formations->close();

    // Étape 3: Supprimer le client après avoir supprimé les attestations et formations
    $delete_client = $conn->prepare("DELETE FROM clients WHERE id = ?");
    $delete_client->bind_param("i", $client_id);
    $delete_client->execute();
    $delete_client->close();

    // Tout s'est bien passé, donc on valide la transaction
    $conn->commit();

    // Rediriger vers la page de liste avec un message de succès
    header("Location: lister.php?message=Client supprimé avec succès");
    exit(); // Assurez-vous que le script s'arrête après la redirection

} catch (Exception $e) {
    // Si une erreur se produit, on annule la transaction
    $conn->rollback();

    // Afficher l'erreur
    echo "Erreur lors de la suppression du client: " . $e->getMessage();
}

// Fermer la connexion
$conn->close();
?>
