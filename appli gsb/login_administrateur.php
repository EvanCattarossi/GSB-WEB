<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion Comptable</title>
    <link href="styles/bootstrap/bootstrap.css" rel="stylesheet">
    <link href="styles/styles.css" rel="stylesheet">
    <style>
       
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Style général pour le body */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Conteneur du formulaire */
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        /* Titre de la page */
        h2 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }

        /* Style pour les labels */
        label {
            font-size: 14px;
            color: #333;
            text-align: left;
            margin-bottom: 5px;
            display: block;
        }

        /* Style pour les champs de saisie */
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            background-color: #fafafa;
            transition: border-color 0.3s;
        }

        /* Effet au focus sur les champs de saisie */
        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #007bff;
            outline: none;
        }

        /* Style pour le bouton de soumission */
        button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        /* Effet au survol du bouton */
        button:hover {
            background-color: #0056b3;
        }

        /* Style pour les messages d'erreur ou d'info */
        .error-message {
            color: #e74c3c;
            font-size: 14px;
            margin-top: 10px;
        }

        .success-message {
            color: #2ecc71;
            font-size: 14px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Connexion Administrateur </h2>
        <form action="login.php" method="post">
            <input type="hidden" name="role" value="comptable">
            <label for="email">Nom d'utilisateur :</label>
            <input type="text" id="email" name="email" required><br><br>
            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password" required><br><br>
            <button type="submit">Se connecter</button>
        </form>
        <a href="dashboard_comptable.php"></a></p>
    </div>
</body>
</html>
