
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
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $nom = $conn->real_escape_string($_POST['nom']);
    $prenom = $conn->real_escape_string($_POST['prenom']);
    $telephone = $conn->real_escape_string($_POST['telephone']);
    $email = $conn->real_escape_string($_POST['email']);
    $mode_paiement = $conn->real_escape_string($_POST['mode_paiement']);
    $montant = $conn->real_escape_string($_POST['montant']);
    $montant_restant = $conn->real_escape_string($_POST['montant_restant']);
    $commentaire = $conn->real_escape_string($_POST['commentaire']);
    
    // Vérifier si un client avec le même nom, prénom et téléphone existe déjà
    $check_sql = "SELECT * FROM clients WHERE nom = '$nom' AND prenom = '$prenom' AND telephone = '$telephone'";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        // Client déjà existant
        echo json_encode(['success' => false, 'message' => 'Erreur : Un client avec le même nom, prénom et téléphone existe déjà.']);
    } else {
        // Insérer les données du client
        $sql = "INSERT INTO clients (nom, prenom, telephone, email, mode_paiement, montant, montant_restant, commentaire) 
                VALUES ('$nom', '$prenom', '$telephone', '$email', '$mode_paiement', '$montant', '$montant_restant', '$commentaire')";

        if ($conn->query($sql) === TRUE) {
            $client_id = $conn->insert_id;

           // Gestion de l'attestation
           if (!empty($_FILES['attestation']['name'])) {
            $attestation_filename = basename($_FILES['attestation']['name']);
            
            // Insérer le chemin de l'attestation dans la table attestations
            $attestation_sql = "INSERT INTO attestations (client_id, fichier) VALUES ('$client_id', '$attestation_filename')";
            $conn->query($attestation_sql);
        }


            // Gestion des formations
            if (!empty($_POST['formation']) && !empty($_POST['session'])) {
                $formation = $conn->real_escape_string($_POST['formation']);
                $session_date = $conn->real_escape_string($_POST['session']);
                $date_fin = $conn->real_escape_string($_POST['date_fin']);
                
                // Rechercher l'ID de la formation
                $formation_query = "SELECT id FROM formations WHERE nom = '$formation'";
                $formation_result = $conn->query($formation_query);
                if ($formation_result->num_rows > 0) {
                    $formation_id = $formation_result->fetch_assoc()['id'];

                    // Insérer la session
                    $session_query = "INSERT INTO sessions (formation_id, session_date , date_fin) VALUES ('$formation_id', '$session_date' ,'$date_fin')";
                    if ($conn->query($session_query) === TRUE) {
                        $session_id = $conn->insert_id;

                        // Associer le client à la formation
                        $client_formation_query = "INSERT INTO client_formations (client_id, session_id) VALUES ('$client_id', '$session_id')";
                        $conn->query($client_formation_query);
                    }
                }
            }

            // Retourner un message de succès
            echo json_encode(['success' => true, 'message' => 'Client enregistré avec succès!']);
        } else {
            // Retourner un message d'erreur
            echo json_encode(['success' => false, 'message' => 'Erreur : ' . $conn->error]);
        }
    }
}

// Fermer la connexion
$conn->close();
?>
