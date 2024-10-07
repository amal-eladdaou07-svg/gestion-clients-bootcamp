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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom_utilisateur = $_POST['nom_utilisateur'];
    $mot_de_passe = $_POST['mot_de_passe'];
    $email = $_POST['email'];

    // Hacher le mot de passe
    $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_BCRYPT);

    // Préparer la requête SQL pour insérer un nouvel utilisateur
    $sql = "INSERT INTO utilisateurs (nom_utilisateur, mot_de_passe, email) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Échec de la préparation de la requête : " . $conn->error);
    }
    $stmt->bind_param("sss", $nom_utilisateur, $mot_de_passe_hash, $email);
    
    // Exécuter la requête
    if ($stmt->execute()) {
        echo "Utilisateur ajouté avec succès.";
    } else {
        echo "Erreur lors de l'ajout de l'utilisateur : " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
