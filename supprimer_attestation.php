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

// Vérifier si l'ID de l'attestation est passé en paramètre
if (isset($_POST['attestation_id'])) {
    $attestation_id = intval($_POST['attestation_id']);

    // Récupérer le nom du fichier à supprimer
    $sql = "SELECT fichier FROM attestations WHERE id=$attestation_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $attestation = $result->fetch_assoc();
        $fichier = $attestation['fichier'];

        // Supprimer l'entrée de l'attestation de la base de données
        $sql_delete = "DELETE FROM attestations WHERE id=$attestation_id";

        if ($conn->query($sql_delete) === TRUE) {
            // Supprimer le fichier du répertoire de téléchargement (si nécessaire)
            $fichier_chemin = "uploads/" . $fichier;
            if (file_exists($fichier_chemin)) {
                unlink($fichier_chemin);
            }
            echo "Attestation supprimée avec succès";
        } else {
            echo "Erreur lors de la suppression de l'attestation: " . $conn->error;
        }
    } else {
        echo "Attestation non trouvée";
    }
} else {
    echo "ID de l'attestation non spécifié";
}

// Fermer la connexion à la base de données
$conn->close();
?>
