<body>
<link rel="stylesheet" href="comptable.css">
    <header>
        <img src="gsb.png" alt="Logo GSB">
        <nav>
            <ul>
                <li><a href="dashboard_comptable.php">Accueil</a></li>
                <li><a href="comptable.php">Consulter Fiche Frais</a></li>
                <li><a href="monProfil.php">Mon Profil</a></li>
                <li><a href="logout.php">Déconnexion</a></li>
            </ul>
        </nav>
    </header>

<?php
session_start();
require 'config.php';

// Vérification que l'utilisateur est connecté et qu'il est comptable
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'comptable') {
    echo "Accès non autorisé.";
    exit();
}

// Si le comptable souhaite traiter une fiche de frais
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['ficheFraisId'])) {
    $ficheFraisId = $_POST['ficheFraisId'];
    $action = $_POST['action'];

    try {
        if ($action === 'rembourser') {
            // Mettre à jour l'état de la fiche de frais en "Remboursé"
            $stmt = $pdo->prepare("UPDATE FichesFrais SET idEtat = 'RB' WHERE id = :ficheFraisId");
            $stmt->bindParam(':ficheFraisId', $ficheFraisId);
            $stmt->execute();
            echo "<p>La fiche de frais a été remboursée avec succès.</p>";
        } elseif ($action === 'refuser') {
            // Rediriger vers la page de description du refus avec l'ID de la fiche
            header("Location: refuse_fiche.php?ficheFraisId=$ficheFraisId");
            exit();
        }
    } catch (PDOException $e) {
        echo "Erreur lors de la mise à jour de la fiche de frais : " . $e->getMessage();
    }
}

// Récupération des fiches de frais pour le comptable
try {
    // Sélection des fiches de frais à consulter, avec les détails associés (utilisateur et état)
    $stmt = $pdo->prepare("SELECT F.id, F.mois, F.montantValide, F.dateModif, E.libelle AS etat, U.nom, U.prenom
                                FROM FichesFrais F
                                JOIN Utilisateurs U ON F.utilisateurId = U.id
                                JOIN Etats E ON F.idEtat = E.id
                                WHERE U.role = 'visiteur'");  // Un comptable peut consulter les fiches des visiteurs
    $stmt->execute();
    $fichesFrais = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Affichage des fiches de frais
    echo "<h2>Liste des fiches de frais des utilisateurs :</h2>";
    if ($fichesFrais) {
        echo "<table border='1'>
                    <tr>
                        <th>ID</th>
                        <th>Nom de l'utilisateur</th>
                        <th>Mois</th>
                        <th>Montant Valide</th>
                        <th>Date de Modification</th>
                        <th>État</th>
                        <th>Actions</th>
                    </tr>";

        foreach ($fichesFrais as $fiche) {
            echo "<tr>
                        <td>{$fiche['id']}</td>
                        <td>{$fiche['nom']} {$fiche['prenom']}</td>
                        <td>{$fiche['mois']}</td>
                        <td>{$fiche['montantValide']}</td>
                        <td>{$fiche['dateModif']}</td>
                        <td>{$fiche['etat']}</td>
                        <td>";
            
            // Formulaire pour rembourser ou refuser la fiche de frais
            if ($fiche['etat'] == 'Créé') {
                echo "
                            <form method='post' action=''>
                                <input type='hidden' name='ficheFraisId' value='{$fiche['id']}'>
                                <button type='submit' name='action' value='rembourser'>Rembourser</button>
                            </form>
                            <form method='post' action=''>
                                <input type='hidden' name='ficheFraisId' value='{$fiche['id']}'>
                                <button type='submit' name='action' value='refuser'>Refuser</button>
                            </form>";
            } else {
                echo "Traitée";
            }

            echo "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Aucune fiche de frais à afficher.</p>";
    }
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des fiches : " . $e->getMessage();
}
?>
</body>