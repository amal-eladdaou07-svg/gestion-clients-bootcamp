// Compteur de formations
let formationCount = 1;

document.addEventListener("DOMContentLoaded", function() {
    // Initialisation des événements
    initEventListeners();
    afficherClients();
});

function initEventListeners() {
    // Événements pour les boutons de navigation
    document.getElementById('btn-ajout').addEventListener('click', afficherFormulaireAjout);
    document.getElementById('btn-modification').addEventListener('click', afficherFormulaireModification);
    document.getElementById('btn-recherche').addEventListener('click', rechercherClient);
    document.getElementById('btn-lister').addEventListener('click', listerClients);
    document.getElementById('btn-accueil').addEventListener('click', retourAccueil);
    // Autres événements
    document.getElementById('btn-enregistrer').addEventListener('click', enregistrerClient);
    document.getElementById('btn-rechercher-mod').addEventListener('click', rechercherClientMod);
}

function afficherFormulaireAjout() {
    window.location.href = 'ajouter.html';
    toggleVisibility('formulaire-modification', 'none');
    toggleVisibility('liste-clients', 'none');
    toggleVisibility('btn-accueil', 'inline-block');
}

function afficherFormulaireModification() {
    window.location.href = 'modifier.html';
    toggleVisibility('formulaire-ajout', 'none');
    toggleVisibility('liste-clients', 'none');
    toggleVisibility('client-info', 'none');
    toggleVisibility('message-modification', 'none');
    toggleVisibility('btn-accueil', 'inline-block');
}

function rechercherClient() {
    const searchInput = document.getElementById('search-input').value.toLowerCase();
    const resultatsTableBody = document.getElementById('resultats');
    
    // Clear results
    resultatsTableBody.innerHTML = '';

    // Fetch clients from server
    fetch('/api/clients')
        .then(response => response.json())
        .then(clients => {
            const foundClients = clients.filter(client => 
                client.nom.toLowerCase().includes(searchInput) ||
                client.prenom.toLowerCase().includes(searchInput) ||
                client.telephone.toLowerCase().includes(searchInput)
            );

            if (foundClients.length === 0) {
                resultatsTableBody.innerHTML = '<tr><td colspan="4">Aucun client trouvé.</td></tr>';
            } else {
                foundClients.forEach(client => {
                    const clientRow = document.createElement('tr');
                    clientRow.innerHTML = `
                        <td>${client.nom}</td>
                        <td>${client.prenom}</td>
                        <td>${client.telephone}</td>
                        <td>${client.email}</td>
                    `;
                    resultatsTableBody.appendChild(clientRow);
                });
            }
        })
        .catch(error => console.error('Erreur de récupération des clients :', error));
}

function rechercherClientMod() {
    const modNom = document.getElementById('mod_nom').value.trim().toLowerCase();
    const modPrenom = document.getElementById('mod_prenom').value.trim().toLowerCase();
    const modTelephone = document.getElementById('mod_telephone').value.trim().toLowerCase();

    fetch('/api/clients')
        .then(response => response.json())
        .then(clients => {
            const foundClient = clients.find(client => 
                client.nom.toLowerCase() === modNom &&
                client.prenom.toLowerCase() === modPrenom &&
                client.telephone.toLowerCase() === modTelephone
            );

            if (foundClient) {
                afficherDetailsClient(clients.indexOf(foundClient));
            } else {
                alert("Client non trouvé !");
            }
        })
        .catch(error => console.error('Erreur de recherche du client :', error));
}

function enregistrerClient() {
    const fileInput = document.getElementById('attestation');
    const attestationFile = fileInput.files[0];

    const formations = [];
    document.querySelectorAll('.formation-entry').forEach(entry => {
        const formation = entry.querySelector('select[name="formation"]').value;
        const session = entry.querySelector('input[name="session"]').value;
        formations.push({ formation, session });
    });

    const client = {
        nom: document.getElementById('nom').value,
        prenom: document.getElementById('prenom').value,
        telephone: document.getElementById('telephone').value,
        email: document.getElementById('email').value,
        attestation: '',
        formations: formations,
        mode_paiement: document.getElementById('mode_paiement').value,
        montant: document.getElementById('montant').value,
        montant_restant: document.getElementById('montant_restant').value,
        commentaire: document.getElementById('commentaire').value
    };

    let formData = new FormData();
    formData.append('client', JSON.stringify(client));
    if (attestationFile) {
        formData.append('attestation', attestationFile);
    }

    fetch('/api/clients', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('form-client').reset();
        toggleVisibility('message', 'block');
        setTimeout(() => {
            toggleVisibility('message', 'none');
        }, 3000);
    })
    .catch(error => console.error('Erreur lors de l\'enregistrement du client :', error));
}

function afficherDetailsClient(index) {
    fetch('/api/clients')
        .then(response => response.json())
        .then(clients => {
            const client = clients[index];

            document.getElementById('client-nom').innerText = client.nom;
            document.getElementById('client-prenom').innerText = client.prenom;
            document.getElementById('client-telephone').innerText = client.telephone;
            document.getElementById('client-email').innerText = client.email;
            document.getElementById('client-attestation').href = client.attestation;
            document.getElementById('client-attestation').innerText = client.attestation ? 'Télécharger l\'attestation' : 'Aucune attestation';
            document.getElementById('client-mode-paiement').innerText = client.mode_paiement;
            document.getElementById('client-montant').textContent = `${client.montant} €`;
            document.getElementById('client-montant_restant').textContent = `${client.montant_restant} €`;
            document.getElementById('client-commentaire').innerText = client.commentaire;

            // Afficher les formations dans un tableau
            const formationsTableBody = document.querySelector('#client-formations-table tbody');
            formationsTableBody.innerHTML = ''; // Vider le tableau avant d'ajouter les nouvelles lignes

            client.formations.forEach(formation => {
                const row = formationsTableBody.insertRow();
                const cellFormation = row.insertCell(0);
                const cellSession = row.insertCell(1);
                cellFormation.innerText = formation.formation;
                cellSession.innerText = formation.session;
            });

            toggleVisibility('client-info', 'block');
        })
        .catch(error => console.error('Erreur de récupération des détails du client :', error));
}

function afficherClients() {
    fetch('/api/clients')
        .then(response => response.json())
        .then(clients => {
            const clientsList = document.getElementById('clients');
            clientsList.innerHTML = '';

            clients.forEach((client, index) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${client.nom}</td>
                    <td>${client.prenom}</td>
                    <td>${client.telephone}</td>
                    <td><a href="mailto:${client.email}">${client.email}</a></td>
                    <td><button onclick="afficherFormulaireModification()">Modifier</button></td>
                    <td><button class="btn-supprimer" onclick="supprimerClient(${index})">Supprimer</button></td>
                `;
                clientsList.appendChild(row);
            });
        })
        .catch(error => console.error('Erreur de récupération des clients :', error));
}

function supprimerClient(index) {
    // Demande confirmation avant de supprimer
    if (confirm("Êtes-vous sûr de vouloir supprimer ce client ?")) {
        fetch(`/api/clients/${index}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(() => afficherClients())
        .catch(error => console.error('Erreur lors de la suppression du client :', error));
    }
}

function listerClients() {
    toggleVisibility('formulaire-ajout', 'none');
    toggleVisibility('formulaire-modification', 'none');
    window.location.href = 'Lister.html';
    toggleVisibility('btn-accueil', 'inline-block');

    const searchInput = document.getElementById('search-clients').value.trim().toLowerCase();

    fetch('/api/clients')
        .then(response => response.json())
        .then(clients => {
            const clientsDiv = document.getElementById('clients');
            clientsDiv.innerHTML = '';

            let filteredClients = clients;

            if (searchInput) {
                filteredClients = filteredClients.filter(client => 
                    client.nom.toLowerCase().includes(searchInput) ||
                    client.prenom.toLowerCase().includes(searchInput) ||
                    client.telephone.toLowerCase().includes(searchInput)
                );
            }

            filteredClients.forEach(client => {
                const clientDiv = document.createElement('div');
                clientDiv.classList.add('client-item');
                clientDiv.innerHTML = `
                    <p><strong>Nom:</strong> ${client.nom}</p>
                    <p><strong>Prénom:</strong> ${client.prenom}</p>
                    <p><strong>Téléphone:</strong> ${client.telephone}</p>
                    <p><strong>Email:</strong> ${client.email}</p>
                `;
                clientsDiv.appendChild(clientDiv);
            });
        })
        .catch(error => console.error('Erreur lors de la liste des clients :', error));
}

function retourAccueil() {
    window.location.href = 'index.html';
    toggleVisibility('formulaire-ajout', 'none');
    toggleVisibility('formulaire-modification', 'none');
    toggleVisibility('client-info', 'none');
}

function toggleVisibility(id, display) {
    document.getElementById(id).style.display = display;
}




// Fonction pour ajouter une nouvelle formation
window.ajouterFormation = function() {
    formationCount++;
    const formationsDiv = document.getElementById('formations');
    const nouvelleFormation = document.createElement('div');
    nouvelleFormation.classList.add('formation-entry');
    nouvelleFormation.setAttribute('data-index', formationCount);
    nouvelleFormation.innerHTML = `
        <label for="formation-${formationCount}">Formation ${formationCount} :</label>
        <select id="formation-${formationCount}" name="formation">
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

                                    <optgroup label="SECURITE:">

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
        <label for="session-${formationCount}">Session :</label>
        <input type="date" id="session-${formationCount}" name="session">
        <button type="button" class="btn-supprimer-formation" onclick="supprimerFormation(this)"><i class="fas fa-minus-circle"></i> Supprimer</button>
    `;
    formationsDiv.appendChild(nouvelleFormation);
    updateFormationNumbers();
};

// Fonction pour supprimer une formation
window.supprimerFormation = function(button) {
    const formationEntry = button.parentElement;
    formationEntry.remove();
    updateFormationNumbers();
};
// Fonction pour mettre à jour les numéros des formations
function updateFormationNumbers() {
const formationEntries = document.querySelectorAll('.formation-entry');
formationEntries.forEach((entry, index) => {
    const formationLabel = entry.querySelector('label[for^="formation"]');
    const formationSelect = entry.querySelector('select[name="formation"]');
    const sessionLabel = entry.querySelector('label[for^="session"]');
    const sessionInput = entry.querySelector('input[name="session"]');

    formationLabel.textContent = `Formation ${index + 1} :`;
    formationLabel.setAttribute('for', `formation-${index + 1}`);
    formationSelect.setAttribute('id', `formation-${index + 1}`);
    sessionLabel.setAttribute('for', `session-${index + 1}`);
    sessionInput.setAttribute('id', `session-${index + 1}`);
});
}
