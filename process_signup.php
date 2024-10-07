<?php
// Connexion à la base de données
$servername = "localhost"; // Remplacez par vos propres paramètres
$username = "root"; // Nom d'utilisateur MySQL
$password = ""; // Mot de passe MySQL
$dbname = "amal"; // Nom de la base de données

$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifiez la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Récupérer les données du formulaire
$nom_utilisateur = $_POST['nom_utilisateur'];
$mot_de_passe = $_POST['password'];
$confirm_mot_de_passe = $_POST['confirm_password'];

// Vérifiez si les mots de passe correspondent
if ($mot_de_passe !== $confirm_mot_de_passe) {
    echo "Les mots de passe ne correspondent pas.";
    exit;
}

// Hashage du mot de passe
$mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_BCRYPT);

// Vérification de l'unicité de l'email
$sql_check = "SELECT id FROM utilisateurs WHERE email = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("s", $nom_utilisateur);
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows > 0) {
    echo "Cet email est déjà utilisé. Veuillez essayer un autre.";
    exit;
}

// Insertion dans la base de données si tout est correct
$sql = "INSERT INTO utilisateurs (nom_utilisateur, email, mot_de_passe, date_creation) VALUES (?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $nom_utilisateur, $nom_utilisateur, $mot_de_passe_hash);

if ($stmt->execute()) {
    echo "Inscription réussie. Vous pouvez maintenant vous connecter.";
    // Redirection vers la page de connexion
    header("Location: login.php");
} else {
    echo "Erreur : " . $sql . "<br>" . $conn->error;
}

// Fermeture de la connexion
$conn->close();
?>
