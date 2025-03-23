<?php
session_start();
require 'config.php';

// Vérification de l'authentification
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$fiche_id = $_GET['id'];

// Récupérer la fiche de frais
$stmt = $pdo->prepare("SELECT * FROM FichesFrais WHERE id = ? AND utilisateurId = ?");
$stmt->execute([$fiche_id, $user_id]);
$fiche = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$fiche) {
    die("Fiche de frais non trouvée.");
}

// Récupérer les frais forfaitisés
$stmt = $pdo->prepare("SELECT * FROM ElementsForfaitises WHERE ficheFraisId = ?");
$stmt->execute([$fiche_id]);
$forfaits = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les frais hors forfait
$stmt = $pdo->prepare("SELECT * FROM ElementsHorsForfait WHERE ficheFraisId = ?");
$stmt->execute([$fiche_id]);
$horsForfaits = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Gestion du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mettre à jour les frais forfaitisés
    foreach ($_POST['forfait'] as $forfait_id => $quantite) {
        $stmt = $pdo->prepare("UPDATE ElementsForfaitises SET quantite = ? WHERE id = ?");
        $stmt->execute([$quantite, $forfait_id]);
    }

    // Mettre à jour les frais hors forfait
    foreach ($_POST['horsForfait'] as $hors_forfait_id => $hors_forfait) {
        $stmt = $pdo->prepare("UPDATE ElementsHorsForfait SET date = ?, libelle = ?, montant = ? WHERE id = ?");
        $stmt->execute([$hors_forfait['date'], $hors_forfait['libelle'], $hors_forfait['montant'], $hors_forfait_id]);
    }

    // Rediriger vers la liste des fiches de frais
    header('Location: afficherFicheFrais.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Fiche de Frais</title>
    <link rel="stylesheet" href="modifier.css"> 
</head>
<body>
<header>
    <img src="gsb.png" alt="Logo GSB">
    <nav>
        <ul>
            <li><a href="index.html">Accueil</a></li>
            <li><a href="FicheFrais.php">Renseigner Fiche Frais</a></li>
            <li><a href="monProfil.php">Mon Profil</a></li>
            <li><a href="logout.php">Déconnexion</a></li>
        </ul>
    </nav>
</header>
<h1>Modifier Fiche de Frais</h1>

<form method="post">
    <h2>Frais Forfaitisés</h2>
    <?php if (empty($forfaits)): ?>
        <p>Aucun frais forfaitisé trouvé.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Type de Frais</th>
                    <th>Quantité</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($forfaits as $forfait): ?>
                    <tr>
                        <td><?= htmlspecialchars($forfait['typeForfait']) ?></td>
                        <td><input type="number" name="forfait[<?= $forfait['id'] ?>]" value="<?= htmlspecialchars($forfait['quantite']) ?>"></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <h2>Frais Hors Forfait</h2>
    <?php if (empty($horsForfaits)): ?>
        <p>Aucun frais hors forfait trouvé.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Libellé</th>
                    <th>Montant</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($horsForfaits as $horsForfait): ?>
                    <tr>
                        <td><input type="date" name="horsForfait[<?= $horsForfait['id'] ?>][date]" value="<?= htmlspecialchars($horsForfait['date']) ?>"></td>
                        <td><input type="text" name="horsForfait[<?= $horsForfait['id'] ?>][libelle]" value="<?= htmlspecialchars($horsForfait['libelle']) ?>"></td>
                        <td><input type="number" name="horsForfait[<?= $horsForfait['id'] ?>][montant]" value="<?= htmlspecialchars($horsForfait['montant']) ?>"></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <button type="submit">Enregistrer les modifications</button>
</form>

</body>
</html>