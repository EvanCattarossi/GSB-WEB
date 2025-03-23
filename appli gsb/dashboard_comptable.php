<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des frais - comptable</title>
    <link rel="stylesheet" href="styles.css"> </head>
    <style>
/* Règles de réinitialisation */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

/* Style de base pour le corps de la page */
body {
  font-family: Arial, sans-serif;
  line-height: 1.6;
  background-color: #f4f4f4;
  color: #333;
}

/* Style de l'en-tête */
header {
  background-color: #007bff; /* Bleu foncé */
  padding: 20px 0;
  text-align: center;
}

/* Style du logo dans l'en-tête */
header img {
  width: 120px;
  height: auto;
}

/* Style de la navigation principale */
nav ul {
  list-style-type: none;
  padding: 0;
}

/* Style des éléments de liste de la navigation */
nav ul li {
  display: inline-block;
  margin: 0 20px;
}

/* Style des liens de navigation */
nav ul li a {
  color: white;
  text-decoration: none;
  font-size: 16px;
  padding: 10px 15px;
  border-radius: 4px;
  transition: background-color 0.3s ease;
}

/* Style des liens de navigation au survol */
nav ul li a:hover {
  background-color: #004c70; /* Bleu plus foncé */
}

/* Style de la section principale */
main {
  padding: 20px;
}

/* Style du titre principal */
h2 {
  font-size: 24px;
  color: #333;
  margin-bottom: 20px;
  text-align: center; /* Centre le titre */
}

/* Style de la navigation secondaire (boutons) */
.secondary-nav {
  margin-top: 30px;
  display: flex; /* Utilise Flexbox pour aligner les boutons */
  justify-content: center; /* Centre les boutons horizontalement */
}

/* Style des boutons */
.secondary-nav .button {
  display: inline-block;
  margin: 10px 15px;
  padding: 15px 30px;
  font-size: 16px;
  font-weight: bold;
  text-decoration: none;
  border-radius: 4px;
  transition: background-color 0.3s ease, transform 0.2s ease;
}

/* Style des boutons verts */
.secondary-nav .button.green {
  background-color: #4caf50; /* Vert */
  color: white;
}

/* Style des boutons bleus */
.secondary-nav .button.blue {
  background-color: #007bff; /* Bleu */
  color: white;
}

/* Effet de survol pour les boutons */
.secondary-nav .button:hover {
  transform: scale(1.05); /* Agrandit légèrement le bouton au survol */
}

    </style>
<body>
    <header>
        <img src="gsb.png" alt="Logo GSB"> <nav>
            <ul>
                <li><a href="dashboard_comptable.php">Accueil</a></li>
                <li><a href="monProfil.php">Mon Profil</a></li>
                <li><a href="logout.php">Déconnexion</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h2>Gestion des frais - Visiteur </h2>
        <nav class="secondary-nav">
        <button class="button btn-green" onclick="window.location.href='rembourssementfrais.php'">renseignement comptable </button>
            <a href="comptable.php" class="button blue">Afficher  fiches de frais</a>
            
        </nav>
    </main>

    <script src="script.js"></script> </body>
</html>