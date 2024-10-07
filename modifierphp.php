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

// Récupérer l'ID du client depuis l'URL
$client_id = intval($_GET['id']);  // Assurez-vous que l'ID est un entier

// Récupérer les informations du client
$sql = "SELECT * FROM clients WHERE id=$client_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $client = $result->fetch_assoc();
} else {
    echo "Client non trouvé";
    exit;
}

// Récupérer les sessions avec date_fin
$sql_formations = "SELECT cf.*, s.session_date, s.date_fin, f.nom AS formation_nom
                   FROM client_formations cf
                   JOIN sessions s ON cf.session_id = s.id
                   JOIN formations f ON s.formation_id = f.id
                   WHERE cf.client_id=$client_id";
$formations = $conn->query($sql_formations);


// Récupérer les attestations
$sql_attestations = "SELECT * FROM attestations WHERE client_id=$client_id";
$attestations = $conn->query($sql_attestations);



?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un Client</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    
</head>
<body>

    <header>
    <img src="images/bootcamp_new.png" alt="Bootcamp Image" alt="Bootcamp Formation" style="text-align: left;">
        <h1><i class="fas fa-user-tie"></i> Modifier un Client</h1>
    </header>
    <div class="card">
    
        <button onclick="window.location.href='lister.php'"><i class="fas fa-arrow-left"></i> Retour</button>
        <h2>Modifier un Client</h2>
        <form action="enregistrer_modifications.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="client_id" value="<?php echo htmlspecialchars($client['id']); ?>">
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($client['nom']); ?>" required>

            <label for="prenom">Prénom :</label>
            <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($client['prenom']); ?>" required>

            <label for="telephone">Téléphone :</label>
            <input type="text" id="telephone" name="telephone" value="<?php echo htmlspecialchars($client['telephone']); ?>">

            <label for="email">Email :</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($client['email']); ?>">
           
            <label for="attestations">Attestations :</label>
            <input type="file" id="attestations" name="attestations[]" accept=".pdf,.doc,.docx" multiple>
            <div id="attestation-lien">
    <?php
    if ($attestations->num_rows > 0) {
        while($attestation = $attestations->fetch_assoc()) {
            echo '<div class="attestation-entry">
                    <a href="uploads/' . htmlspecialchars($attestation['fichier']) . '" download>' . htmlspecialchars($attestation['fichier']) . '</a>
                    <button type="button" class="btn-supprimer-attestation" onclick="supprimerAttestation(this, ' . htmlspecialchars($attestation['id']) . ')"><i class="fas fa-minus-circle"></i> Supprimer</button>
                  </div>';
        }
    }
    ?>
</div>


<div id="formations">
    <?php
    if ($formations->num_rows > 0) {
        while($formation = $formations->fetch_assoc()) {
            echo '<div class="formation-entry">
                    <label for="formation-'.$formation['id'].'">Formation :</label>
                    <input type="text" name="formation[]" value="'.$formation['formation_nom'].'" readonly>
                    <label for="session-'.$formation['id'].'">Date de début :</label>
                    <input type="date" name="session[]" value="'.$formation['session_date'].'" readonly>
                    <label for="date_fin-'.$formation['id'].'">Date de Fin :</label>
                    <input type="date" name="date_fin[]" value="'.$formation['date_fin'].'">
                    <button type="button" class="btn-supprimer-formation" onclick="supprimerFormation(this)"><i class="fas fa-minus-circle"></i> Supprimer</button>
                  </div>';
        }
    }
    ?>
</div>

            <button type="button" class="btn-ajouter-formation" onclick="ajouterFormation()"><i class="fas fa-plus-circle"></i> Ajouter une formation</button>

            <label for="mode_paiement">Mode de Paiement :</label>
            <select id="mode_paiement" name="mode_paiement">
                <option value="virement" <?php echo $client['mode_paiement'] == 'virement' ? 'selected' : ''; ?>>Virement</option>
                <option value="cash" <?php echo $client['mode_paiement'] == 'cash' ? 'selected' : ''; ?>>Cash</option>
                <option value="cheque" <?php echo $client['mode_paiement'] == 'cheque' ? 'selected' : ''; ?>>Chèque</option>
                <option value="clf" <?php echo $client['mode_paiement'] == 'clf' ? 'selected' : ''; ?>>Clf</option>
            </select>

            <div class="montant-container">
                <label for="montant">Montant Payé:</label>
                <div class="input-group">
                    <input type="number" id="montant" name="montant" step="0.00001" min="0" value="<?php echo htmlspecialchars($client['montant']); ?>" required>
                    <i class="fas fa-euro-sign"></i>
                </div>
            </div>

            <div class="montant-container">
                <label for="montant_restant">Montant Restant:</label>
                <div class="input-group">
                    <input type="number" id="montant_restant" name="montant_restant" step="0.00001" min="0" value="<?php echo htmlspecialchars($client['montant_restant']); ?>" required>
                    <i class="fas fa-euro-sign"></i>
                </div>
            </div>

            <label for="commentaire">Commentaire :</label>
            <textarea id="commentaire" name="commentaire"><?php echo htmlspecialchars($client['commentaire']); ?></textarea>

            <button type="submit"><i class="fas fa-save"></i> Enregistrer</button>
            <div id="message" class="message">Modifications enregistrées avec succès!</div>
        </form>
    </div>
    <script>
       function ajouterFormation() {
    const formationsDiv = document.getElementById('formations');
    const newIndex = formationsDiv.childElementCount;
    const div = document.createElement('div');
    div.className = 'formation-entry';
    div.innerHTML = `
        <label for="formation-${newIndex}">Formation :</label>
        <select name="formation[]">
            <optgroup label="ÉLECTRICITÉ:">
                            <option value="Formation travaux sous tension en BT sur les installations – Batteries d’accumulateurs stationnaires">Formation travaux sous tension en BT sur les installations – Batteries d’accumulateurs stationnaires</option>
                        <option value="Formation Signalisation Lumineuse Tricolore (SLT) – Niveau 1">Formation Signalisation Lumineuse Tricolore (SLT) – Niveau 1</option>
                        <option value="Formation Signalisation Lumineuse Tricolore (SLT) – Niveau 2">Formation Signalisation Lumineuse Tricolore (SLT) – Niveau 2</option>
                        <option value="Formation Eclairage Publique – Niveau 4">Formation Eclairage Publique – Niveau 4</option>
                        
                    </select>
                    
        <label for="session-${newIndex}">Date de début :</label>
        <input type="date" name="session[]">
        <label for="date_fin-${newIndex}">Date de Fin :</label>
        <input type="date" name="date_fin[]">
        <button type="button" class="btn-supprimer-formation" onclick="supprimerFormation(this)"><i class="fas fa-minus-circle"></i> Supprimer</button>
    `;
    formationsDiv.appendChild(div);
}


        function supprimerFormation(button) {
            const formationDiv = button.parentNode;
            formationDiv.remove();
        }
        function supprimerAttestation(button, attestationId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette attestation?')) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'supprimer_attestation.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    button.parentNode.remove();
                } else {
                    alert('Erreur lors de la suppression de l\'attestation.');
                }
            }
        };
        xhr.send('id=' + encodeURIComponent(attestationId));
    }
}

    </script>
</body>
</html>
