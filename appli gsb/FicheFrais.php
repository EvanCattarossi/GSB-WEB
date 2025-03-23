<?php
session_start();
require 'config.php'; // Fichier pour se connecter à la base de données

// Vérification de l'authentification
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Récupérer les données de l'utilisateur connecté
$user_id = $_SESSION['user_id'];

// Gestion du formulaire de soumission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer la date choisie ou utiliser la date actuelle si non fournie
    $dateFiche = isset($_POST['dateFiche']) ? $_POST['dateFiche'] : date('Y-m-d');
    $mois = date('Ym', strtotime($dateFiche)); // Mois en format AAAAMM basé sur la date de la fiche
    $dateModif = date('Y-m-d');

    // Insérer ou récupérer la fiche de frais
    $stmt = $pdo->prepare("SELECT id FROM FichesFrais WHERE utilisateurId = ? AND mois = ?");
    $stmt->execute([$user_id, $mois]);
    $fiche = $stmt->fetch();

    if (!$fiche) {
        // Créer une nouvelle fiche si elle n'existe pas
        $stmt = $pdo->prepare("INSERT INTO FichesFrais (utilisateurId, mois, dateModif) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $mois, $dateModif]);
        $ficheId = $pdo->lastInsertId();
    } else {
        $ficheId = $fiche['id'];
    }

    // Calcul du montant validé
    $montantValide = 0;

    // Gérer les frais forfaitisés
    if (!empty($_POST['forfait'])) {
        foreach ($_POST['forfait'] as $typeForfait => $quantite) {
            $quantite = (int) $quantite;
            if ($quantite > 0) {
                $stmt = $pdo->prepare("INSERT INTO ElementsForfaitises (ficheFraisId, typeForfait, quantite, montant) 
                                        VALUES (?, ?, ?, (SELECT montant FROM FraisForfait WHERE id = ?)) 
                                        ON DUPLICATE KEY UPDATE quantite = VALUES(quantite)");
                $stmt->execute([$ficheId, $typeForfait, $quantite, $typeForfait]);

                // Récupérer le montant unitaire du forfait
                $stmtMontant = $pdo->prepare("SELECT montant FROM FraisForfait WHERE id = ?");
                $stmtMontant->execute([$typeForfait]);
                $montantUnitaire = $stmtMontant->fetchColumn();

                $montantValide += $quantite * $montantUnitaire;
            }
        }
    }

    // Gérer les frais hors forfait
    if (!empty($_POST['horsForfait'])) {
        foreach ($_POST['horsForfait'] as $horsFrais) {
            if (!empty($horsFrais['libelle']) && !empty($horsFrais['montant'])) {
                $stmt = $pdo->prepare("INSERT INTO ElementsHorsForfait (ficheFraisId, date, libelle, montant) 
                                        VALUES (?, ?, ?, ?)");
                $stmt->execute([$ficheId, $horsFrais['date'], $horsFrais['libelle'], $horsFrais['montant']]);

                $montantValide += (float) $horsFrais['montant'];
            }
        }
    }

    // Mettre à jour le montant validé dans la fiche de frais
    $stmtUpdate = $pdo->prepare("UPDATE FichesFrais SET montantValide = ? WHERE id = ?");
    $stmtUpdate->execute([$montantValide, $ficheId]);

    $_SESSION['success'] = "Fiche de frais mise à jour avec succès.";
    header("Location: FicheFrais.php");
    exit();
}

// Récupérer les frais forfaitisés disponibles
$forfaits = $pdo->query("SELECT id, libelle, montant FROM FraisForfait")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche de Frais</title>
    <link rel="stylesheet" href="fiches.css">
</head>
<body>
    <header>
        <img src="gsb.png" alt="Logo GSB">
        <nav>
            <ul>
                <li><a href="index.html">Accueil</a></li>
                <li><a href="AfficherFicheFrais.php">Afficher Fiche Frais</a></li>
                <li><a href="monProfil.php">Mon Profil</a></li>
                <li><a href="logout.php">Déconnexion</a></li>
            </ul>
        </nav>
    </header>
    <h1>Remplir votre fiche de frais</h1>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <form action="ficheFrais.php" method="POST">
        <h2>Choisir la date de la fiche de frais</h2>
        <label for="dateFiche">Date de la fiche de frais:</label>
        <input type="date" id="dateFiche" name="dateFiche" required>

        <h2>Frais Forfaitisés</h2>
        <?php foreach ($forfaits as $forfait): ?>
            <div>
                <label for="forfait_<?= $forfait['id']; ?>"><?= htmlspecialchars($forfait['libelle']); ?> (<?= number_format($forfait['montant'], 2); ?> € / unité):</label>
                <input type="number" id="forfait_<?= $forfait['id']; ?>" name="forfait[<?= $forfait['id']; ?>]" min="0" value="0">
            </div>
        <?php endforeach; ?>

        <h2>Frais Hors Forfait</h2>
        <div id="horsForfaitContainer">
            <div class="horsForfaitItem">
                <label>Date: <input type="date" name="horsForfait[0][date]" required></label>
                <label>Libellé: <input type="text" name="horsForfait[0][libelle]" required></label>
                <label>Montant (€): <input type="number" step="0.01" name="horsForfait[0][montant]" required></label>
            </div>
        </div>
        <button type="button" id="addHorsForfait">Ajouter un frais hors forfait</button>

        <button type="submit">Soumettre</button>
    </form>

    <script>
        let horsForfaitCount = 1;
        document.getElementById('addHorsForfait').addEventListener('click', function () {
            const container = document.getElementById('horsForfaitContainer');
            const newItem = document.createElement('div');
            newItem.className = 'horsForfaitItem';
            newItem.innerHTML = `
                <label>Date: <input type="date" name="horsForfait[${horsForfaitCount}][date]" required></label>
                <label>Libellé: <input type="text" name="horsForfait[${horsForfaitCount}][libelle]" required></label>
                <label>Montant (€): <input type="number" step="0.01" name="horsForfait[${horsForfaitCount}][montant]" required></label>
            `;
            container.appendChild(newItem);
            horsForfaitCount++;
        });
    </script>
</body>
</html>