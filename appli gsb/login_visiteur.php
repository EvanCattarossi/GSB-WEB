<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion Visiteur Médical</title>
    <link href="styles/bootstrap/bootstrap.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
    <style>
      
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

       
        h2 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }

        label {
            font-size: 14px;
            color: #333;
            text-align: left;
            margin-bottom: 5px;
            display: block;
        }

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

       
        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #007bff;
            outline: none;
        }

      
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

       
        button:hover {
            background-color: #0056b3;
        }

        
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
        <h2>Connexion Visiteur Médical</h2>
        <form action="login.php" method="post">
            <input type="hidden" name="role" value="visiteur">
            <label for="email">Nom d'utilisateur :</label>
            <input type="text" id="email" name="email" required><br><br>
            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password" required><br><br>
            <button type="submit">Se connecter</button>
        </form>
        <a href="dashboard_visiteur.php"></a></p>
</body> 
    </div>
</body>
</html>
