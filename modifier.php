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
    <style> 
    .attestation-entry {
            margin-bottom: -14px;
        }
        .alert.success {
            color: #008000; /* Green */
        }
        .alert.error {
            color: #cc0000; /* Red */
        }
        .alert.info {
            color: orange;
        }
        </style>
    
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
            <input type="file" id="attestations" name="attestations[]" accept=".pdf,.doc,.docx,.jpg" multiple>
            <div id="attestation-lien">
                <?php
            if ($attestations->num_rows > 0) {
    echo '<ul class="attestations-list">';  // Commencez une liste non ordonnée
    while($attestation = $attestations->fetch_assoc()) {
        echo '<li class="attestation-entry">';  // Commencez un élément de liste
        echo '<a href="uploads/' . htmlspecialchars($attestation['fichier']) . '" download>' . htmlspecialchars($attestation['fichier']) . '</a>';
        echo '<button type="button" class="btn-supprimer-attestation" onclick="supprimerAttestation(this, ' . htmlspecialchars($attestation['id']) . ')"><i class="fas fa-minus-circle"></i> Supprimer</button>';
        echo '</li>';  // Fermez l'élément de liste
    }
    echo '</ul>';  // Fermez la liste non ordonnée
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
            <div id="message" class="message" style="<?php echo isset($_GET['message']) ? 'display: block;' : 'display: none;'; ?>">
            <?php
if (isset($_GET['message'])) {
    if ($_GET['message'] == 'success') {
        echo "<div class='alert success'>Modifications enregistrées avec succès!</div>";
    } elseif ($_GET['message'] == 'error') {
        echo "<div class='alert error'>Erreur lors de l'enregistrement des modifications.</div>";
    } elseif ($_GET['message'] == 'no_changes') {
        echo "<div class='alert info'>Aucune modification effectuée.</div>";
    }
}
?>

</div>



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
                        <option value="Formation Eclairage Publique – Niveau 3">Formation Eclairage Publique – Niveau 3</option>
                        <option value="Formation Colonne Montante – Module Chargé d’Affaires">Formation Colonne Montante – Module Chargé d’Affaires</option>
                        <option value="Formation Colonne Montante – Module Technique Installateur">Formation Colonne Montante – Module Technique Installateur</option>
                        <option value="Formation Eclairage Publique – Niveau 2">Formation Eclairage Publique – Niveau 2</option>
                        <option value="Formation Eclairage Publique – Niveau 1">Formation Eclairage Publique – Niveau 1</option>
                        <option value="Formation électricité – Niveau 2 : Installation électrique dans les logements">Formation électricité – Niveau 2 : Installation électrique dans les logements</option>
                        <option value="Formation électricité – Niveau 1 : Bases de l’électricité">Formation électricité – Niveau 1 : Bases de l’électricité</option>
                    </optgroup>

                        <optgroup label="MAINTENANCE INDUSTRIELLE:">
                            <option value="Formation Maintenance Préventive">Formation Maintenance Préventive</option>

                        <optgroup label="MANAGEMENT:">
                            <option value="Formation SECRETAIRE COMPTABLE – 18 heures">Formation SECRETAIRE COMPTABLE – 18 heures</option>
                            <option value="Formation Encadrement d’Equipe">Formation Encadrement d’Equipe</option>
                            <option value="Formation Gestion de Chantier">Formation Gestion de Chantier</option>
                            <option value="Formation Rédaction du DUER (Document Unique d’Évaluation des Risques)">Formation Rédaction du DUER (Document Unique d’Évaluation des Risques)</option>

                            <optgroup label="TELECOM VDI:">
                                <option value="Formation Installation Alarme et Contrôle d’accès">Formation Installation Alarme et Contrôle d’accès</option>
                                <option value="Formation Installation Vidéo Protection et Vidéo Surveillance">Formation Installation Vidéo Protection et Vidéo Surveillance</option>
                                <option value="Formation Solutions Domotiques">Formation Solutions Domotiques</option>

                            <optgroup label="INSERTION PROFESSIONNELLE:">  
                                <option value="Formation Monteur Raccordeur Mobile GSM 4G/5G (INSERTION)">Formation Monteur Raccordeur Mobile GSM 4G/5G (INSERTION)</option>
                                <option value="Formation Technicien VDI Multitechnique – Spécialisation domotique, alarme, vidéo-surveillance (INSERTION)">Formation Technicien VDI Multitechnique – Spécialisation domotique, alarme, vidéo-surveillance (INSERTION)</option>
                                <option value="Formation Technicien VDI Multitechnique – Spécialisation domotique, alarme, vidéo-surveillance (RECONVERSION)">Formation Technicien VDI Multitechnique – Spécialisation domotique, alarme, vidéo-surveillance (RECONVERSION)</option>
                                <option value="Formation Monteur Raccordeur Fibre FTTH (RECONVERSION)">Formation Monteur Raccordeur Fibre FTTH (RECONVERSION)</option>
                                <option value="Formation Monteur Raccordeur Fibre FTTH (INSERTION)">Formation Monteur Raccordeur Fibre FTTH (INSERTION)</option>
                                <option value="Formation Aide Electricien du Bâtiment – Spécialisation Transition Énergétique (RECONVERSION)">Formation Aide Electricien du Bâtiment – Spécialisation Transition Énergétique (RECONVERSION)</option>
                                <option value="Formation Aide Electricien du Bâtiment – Spécialisation Transition Énergétique (INSERTION)">Formation Aide Electricien du Bâtiment – Spécialisation Transition Énergétique (INSERTION)</option>
                                <option value="Formation Aide Installateur Monteur IRVE">Formation Aide Installateur Monteur IRVE</option>
                                <option value="Formation Monteur Raccordeur Mobile GSM 4G/5G (RECONVERSION)">Formation Monteur Raccordeur Mobile GSM 4G/5G (RECONVERSION)</option>
                                <option value="Formation Monteur Raccordeur Fibre FTTH (POEC)">Formation Monteur Raccordeur Fibre FTTH (POEC)</option>
                                <option value="Formation Monteur Raccordeur Mobile GSM 4G/5G (POEC)">Formation Monteur Raccordeur Mobile GSM 4G/5G (POEC)</option>

                            <optgroup label="Transition Energétique:"> 
                                <option value="Parcours TPE Artisan option IRVE – B_TPE_IRVE">Parcours TPE Artisan option IRVE – B_TPE_IRVE</option>
                                <option value="Formation Panneau Photovoltaïque – QUALI SPV1 SPV2">Formation Panneau Photovoltaïque – QUALI SPV1 SPV2 </option>
                                <option value="Formation Pompe à chaleur – QUALI’PAC">Formation Pompe à chaleur – QUALI’PAC</option>
                                <option value="Formation Panneau Photovoltaïque – QUALI’PV BAT">Formation Panneau Photovoltaïque – QUALI’PV BAT</option>
                                <option value="Formation Chantier PV – Panneaux Photovoltaïques">Formation Chantier PV – Panneaux Photovoltaïques</option>
                                <option value="Formation Panneau Solaire – QUALI’SOL CESI">Formation Panneau Solaire – QUALI’SOL CESI</option>
                                <option value="Formation Panneau Solaire – QUALI’SOL SSC">Formation Panneau Solaire – QUALI’SOL SSC</option>
                                <option value="Formation Panneau Photovoltaïque – QUALI’PV HAUTE PUISSANCE">Formation Panneau Photovoltaïque – QUALI’PV HAUTE PUISSANCE</option>
                                <option value="Formation Panneau Photovoltaïque – QUALI’PV ELEC">Formation Panneau Photovoltaïque – QUALI’PV ELEC</option>
                                <option value="Formation Etude IRVE">Formation Etude IRVE</option>
                                <option value="Formation Maintenance IRVE – MA1">Formation Maintenance IRVE – MA1</option>
                                <option value="Formation Installation IRVE P1 P2 P3">Formation Installation IRVE P1 P2 P3</option>
                                <option value="Formation Installation IRVE P1 P2">Formation Installation IRVE P1 P2</option>
                                <option value="Formation Chantier IRVE">Formation Chantier IRVE</option>

                                <optgroup label="INFORMATIQUE:">
                                    <option value="Formation BOOTSTRAP – 15 heures">Formation BOOTSTRAP – 15 heures</option>
                                    <option value="Formation Technicien d’assistance en Informatique – 40 heures">Formation Technicien d’assistance en Informatique – 40 heures</option>
                                    <option value="Formation Concepteur designer UI – 14 heures">Formation Concepteur designer UI – 14 heures</option>
                                    <option value="Formation HTML-CSS – 15 heures">Formation HTML-CSS – 15 heures</option>
                                    <option value="Formation GIT-HUB – 10 heures">Formation GIT-HUB – 10 heures</option>
                                    <option value="Formation AUTODESK REVIT – 22 heures">Formation AUTODESK REVIT – 22 heures</option>
                                    <option value="Formation LANGUAGE C++ – 40 heures">Formation LANGUAGE C++ – 40 heures</option>
                                    <option value="Formation INTERACTIVE JQUERY – 10 heures">Formation INTERACTIVE JQUERY – 10 heures</option>
                                    <option value="Formation PYTHON – 9 heures">Formation PYTHON – 9 heures</option>
                                    <option value="Formation CYBER SÉCURITÉ – 35 heures">Formation CYBER SÉCURITÉ – 35 heures</option>
                                    <option value="Formation SASS – 7 heures">Formation SASS – 7 heures</option>
                                    <option value="Formation HTML-CSS – 14 heures">Formation HTML-CSS – 14 heures</option>
                                    <option value="Formation JAVA SCRIPT – 14 heures">Formation JAVA SCRIPT – 14 heures</option>
                                    <option value="Formation JAVA SCRIPT – 7 heures">Formation JAVA SCRIPT – 7 heures</option>
                                    <option value="Formation GIT-HUB – 4 heures">Formation GIT-HUB – 4 heures</option>
                                    <option value="Formation GIT-HUB – 1 heure">Formation GIT-HUB – 1 heure</option>
                                    <option value="Formation TECHNICIEN RESEAUX IP – 21 heures">Formation TECHNICIEN RESEAUX IP – 21 heures</option>
                                    <option value="Formation TECHNICIEN RESEAUX IP – 14 heures">Formation TECHNICIEN RESEAUX IP – 14 heures</option>
                                    <option value="Formation TECHNICIEN RESEAUX IP – 7 heures">Formation TECHNICIEN RESEAUX IP – 7 heures</option>
                                    <option value="Formation TECHNICIEN ETUDE EN MECANIQUE – 21 heures">Formation TECHNICIEN ETUDE EN MECANIQUE – 21 heures</option>
                                    <option value="Formation TECHNICIEN ETUDE EN MECANIQUE – 14 heures">Formation TECHNICIEN ETUDE EN MECANIQUE – 14 heures</option>
                                    <option value="Formation TECHNICIEN ETUDE EN MECANIQUE – 7 heures">Formation TECHNICIEN ETUDE EN MECANIQUE – 7 heures</option>
                                    <option value="Formation ADOBE ILLUSTRATOR INITIATION – 20 heures">Formation ADOBE ILLUSTRATOR INITIATION – 20 heures</option>
                                    <option value="Formation ADOBE ILLUSTRATOR INITIATION – 15 heures">Formation ADOBE ILLUSTRATOR INITIATION – 15 heures</option>
                                    <option value="Formation ADOBE ILLUSTRATOR INITIATION – 10 heures">Formation ADOBE ILLUSTRATOR INITIATION – 10 heures</option>
                                    <option value="Formation AUTOCAD – 21 heures">Formation AUTOCAD – 21 heures</option>
                                    <option value="Formation AUTOCAD – 14 heures">Formation AUTOCAD – 14 heures</option>
                                    <option value="Formation AUTODESK REVIT – 14 heures">Formation AUTODESK REVIT – 14 heures</option>
                                    <option value="Formation AUTOCAD – 7 heures">Formation AUTOCAD – 7 heures</option>
                                    <option value="Formation AUTODESK REVIT – 21 heures">Formation AUTODESK REVIT – 21 heures</option>
                                    <option value="Formation AUTODESK REVIT – 7 heures">Formation AUTODESK REVIT – 7 heures</option>
                                    <option value="Formation PROGRAMMATION PHP/SQL – 12 heures">Formation PROGRAMMATION PHP/SQL – 12 heures</option>

                                    <optgroup label="SÉCURITÉ:">

                        <option value="Formation Chariot Elévateur à conducteur porté – CACES R489 – Cat 3">Formation Chariot Elévateur à conducteur porté – CACES R489 – Cat 3</option>
                        <option value="Formation Chariot Elévateur à conducteur porté – CACES R489 – Cat 2">Formation Chariot Elévateur à conducteur porté – CACES R489 – Cat 2</option>
                        <option value="Formation Plateformes Elévatrices – CACES R486 Cat B – Multidirectionnelle">Formation Plateformes Elévatrices – CACES R486 Cat B – Multidirectionnelle</option>
                        <option value="Formation Engins de chantier – CACES R482 – Cat D">Formation Engins de chantier – CACES R482 – Cat D</option>
                        <option value="Formation Engins de chantier – CACES R482 – Cat C3">Formation Engins de chantier – CACES R482 – Cat C3</option>
                        <option value="Formation Engins de chantier – CACES R482 – Cat C2">Formation Engins de chantier – CACES R482 – Cat C2</option>
                        <option value="Formation Échafaudage Roulant – CACES R457">Formation Échafaudage Roulant – CACES R457</option>
                        <option value="Formation Echaffaudage Fixe – CACES R408">Formation Echaffaudage Fixe – CACES R408</option>
                        <option value="Formation Chariot Elévateur à conducteur porté – CACES R489 – Cat 2B">Formation Chariot Elévateur à conducteur porté – CACES R489 – Cat 2B</option>
                        <option value="Formation Engins de chantier – CACES R482 – Cat A">Formation Engins de chantier – CACES R482 – Cat A</option>
                        <option value="Formation Chariot Elévateur à conducteur porté – CACES R489 – Cat 6">Formation Chariot Elévateur à conducteur porté – CACES R489 – Cat 6</option>
                        <option value="Formation Chariot Elévateur à conducteur porté – CACES R489 – Cat 1B">Formation Chariot Elévateur à conducteur porté – CACES R489 – Cat 1B</option>
                        <option value="Formation Chariot Elévateur à conducteur porté – CACES R489 – Cat 1A">Formation Chariot Elévateur à conducteur porté – CACES R489 – Cat 1A</option>
                        <option value="Formation Engins de chantier – CACES R482 – Cat G">Formation Engins de chantier – CACES R482 – Cat G</option>
                        <option value="Formation Engins de chantier – CACES R482 – Cat F">Formation Engins de chantier – CACES R482 – Cat F</option>
                        <option value="Formation Engins de chantier – CACES R482 – Cat C1">Formation Engins de chantier – CACES R482 – Cat C1</option>
                        <option value="Formation Engins de chantier – CACES R482 – Cat B">Formation Engins de chantier – CACES R482 – Cat B</option>
                        <option value="Formation Chariot Elévateur à conducteur porté – CACES R489 – Cat 4">Formation Chariot Elévateur à conducteur porté – CACES R489 – Cat 4</option>
                        <option value="Formation Chariot Elévateur à conducteur porté – CACES R489 – Cat 2A">Formation Chariot Elévateur à conducteur porté – CACES R489 – Cat 2A</option>
                        <option value="Formation Chariot Elévateur à conducteur porté – CACES R489 – Cat 5">Formation Chariot Elévateur à conducteur porté – CACES R489 – Cat 5</option>
                        <option value="Formation Grue à tour – CACES R487">Formation Grue à tour – CACES R487</option>
                        <option value="Habilitation électrique BP – BR Photovoltaïque">Habilitation électrique BP – BR Photovoltaïque</option>
                        <option value="Recyclage Habilitation électrique B1V, B2V, B2V Essai, BR, BC, H0V">Recyclage Habilitation électrique B1V, B2V, B2V Essai, BR, BC, H0V</option>
                        <option value="Recyclage Habilitation électrique B0, H0, H0V, BS, BE Manoeuvre">Recyclage Habilitation électrique B0, H0, H0V, BS, BE Manoeuvre</option>
                        <option value="Formation Plateformes Elévatrices – CACES R486 Cat A – Vertical">Formation Plateformes Elévatrices – CACES R486 Cat A – Vertical</option>
                        <option value="Habilitation électrique B0, H0, H0V, BS, BE Manoeuvre">Habilitation électrique B0, H0, H0V, BS, BE Manoeuvre/option>
                        <option value="Habilitation Travail en hauteur et Port du harnais">Habilitation Travail en hauteur et Port du harnais</option>
                        <option value="Formation Sauveteur Secouriste du Travail – Maintien et Actualisation des Compétences">Formation Sauveteur Secouriste du Travail – Maintien et Actualisation des Compétences</option>
                        <option value="Formation Sauveteur Secouriste du Travail (SST)">Formation Sauveteur Secouriste du Travail (SST)</option>
                        <option value="Habilitation électrique B1V, B2V, B2V Essai, BR, BC, H0V">Habilitation électrique B1V, B2V, B2V Essai, BR, BC, H0V</option>
                        <option value="Habilitation électrique B1V, B2V, B2V Essai, H0V">Habilitation électrique B1V, B2V, B2V Essai, H0V</option>
                        <option value="Habilitation électrique B0, H0, H0V">Habilitation électrique B0, H0, H0V</option>
                        <option value="Habilitation Manipulation des fluides frigorigènes.">Habilitation Manipulation des fluides frigorigènes.</option>
                        <option value="Formation AIPR – Encadrant">Formation AIPR – Encadrant</option>
                        <option value="Formation AIPR – Opérateur">Formation AIPR – Opérateur</option>
                        <option value="Formation AIPR – Concepteur">Formation AIPR – Concepteur</option>
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
function supprimerAttestation(button, attestationId) {
    if (confirm("Êtes-vous sûr de vouloir supprimer cette attestation ?")) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "supprimer_attestation.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                if (xhr.responseText.trim() === "Attestation supprimée avec succès") {
                    // Supprimer l'élément de la liste dans le DOM
                    var entry = button.closest(".attestation-entry");
                    entry.remove();
                } else {
                    alert("Erreur: " + xhr.responseText);
                }
            }
        };
        xhr.send("attestation_id=" + attestationId);
    }
}


    </script>
</body>
</html>
