<body>
<link rel="stylesheet" href="refus.css">
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

// Vérification de l'ID de la fiche de frais dans l'URL
if (isset($_GET['ficheFraisId'])) {
    $ficheFraisId = $_GET['ficheFraisId'];

    // Si le formulaire est soumis
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['raison_refus'])) {
        $raisonRefus = $_POST['raison_refus'];

        try {
            // Enregistrement du refus dans la base de données
            $stmt = $pdo->prepare("UPDATE FichesFrais SET idEtat = 'RF', raisonRefus = :raisonRefus WHERE id = :ficheFraisId");
            $stmt->bindParam(':raisonRefus', $raisonRefus);
            $stmt->bindParam(':ficheFraisId', $ficheFraisId);
            $stmt->execute();

            echo "<p>La fiche de frais a été refusée avec succès.</p>";
        } catch (PDOException $e) {
            echo "Erreur lors de l'enregistrement du refus : " . $e->getMessage();
        }
    }
} else {
    echo "Aucune fiche de frais spécifiée.";
}
?>

<h2>Raison du refus de la fiche de frais</h2>
<form method="post" action="">
    <textarea name="raison_refus" rows="4" cols="50" placeholder="Entrez la raison du refus..."></textarea><br>
    <button type="submit">Soumettre le refus</button>
</form>

</body>