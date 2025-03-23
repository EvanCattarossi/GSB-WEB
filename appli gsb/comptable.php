<body>
<link rel="stylesheet" href="comptable.css">
    <header>
        <img src="gsb.png" alt="Logo GSB"> <nav>
            <ul>
                <li><a href="dashboard_comptable.php">Accueil</a></li>
                <li><a href="rembourssementfrais.php">rembourssement frais</a></li>
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

// Récupération des fiches de frais pour le comptable
try {
    // Sélection des fiches de frais à consulter, avec les détails associés (utilisateur et état)
    $stmt = $pdo->prepare("
        SELECT F.id, F.mois, F.montantValide, F.dateModif, E.libelle AS etat, U.nom, U.prenom
        FROM FichesFrais F
        JOIN Utilisateurs U ON F.utilisateurId = U.id
        JOIN Etats E ON F.idEtat = E.id
        WHERE U.role = 'visiteur'  -- Un comptable peut consulter les fiches des visiteurs
    ");
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
                </tr>";

        foreach ($fichesFrais as $fiche) {
            echo "<tr>
                    <td>{$fiche['id']}</td>
                    <td>{$fiche['nom']} {$fiche['prenom']}</td>
                    <td>{$fiche['mois']}</td>
                    <td>{$fiche['montantValide']}</td>
                    <td>{$fiche['dateModif']}</td>
                    <td>{$fiche['etat']}</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Aucune fiche de frais à afficher.</p>";
    }
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des fiches : " . $e->getMessage();
}
?>
