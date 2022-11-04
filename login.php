<?php session_start(); ?>

<head>
    <title>Login Foodbook</title>

    <meta charset="utf-8">

    <style>
        <?php require 'styles/login.css'; ?><?php require 'styles/must-have.css'; ?><?php require 'scripts/body-scripts.php'; ?><?php require 'scripts/db.php'; ?>
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <?php RenderFavicon(); ?>
</head>

<body>
    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST')
        AddAnimation();
    ?>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (
            !empty($_POST['email-input'])
            && !empty($_POST['password-input'])
        ) {
            if (VerifyUser($_POST['email-input'], $_POST['password-input'])) {
                $UserInfo = UserInfo($_POST['email-input']);
                $_SESSION['idUser'] = $UserInfo[0];
                $_SESSION['nom'] = $UserInfo[1];
                $_SESSION['prenom'] = $UserInfo[2];
                $_SESSION['email'] = $UserInfo[3];
                echo "<script>window.location.href='index.php'</script>";
            } else {
                echo '
                <script>
                    window.onload = () => { document.getElementById("error_connexion").style.display = "block"; }
                </script>';
            }
        } else {
            echo '  
            <script>
                window.onload = () => { document.getElementById("error_entries").style.display = "block"; }
            </script>';
        }
    }
    ?>
    <div class="header-banner">
        <a href="index.php"><?php echo file_get_contents("utilities/foodbook-logo.svg"); ?></a>
        <div class="banner-title"> Login </div>
    </div>

    <form class="wrapper" method="POST">

        <input class="text-input" name="email-input" type="text" placeholder="Addresse courriel">
        <input type="password" name="password-input" class="text-input" placeholder="Mot de passe">

        <div class="error_message" id="error_entries">Tous les champs sont obligatoires</div>
        <div class="error_message" id="error_connexion">Nom d'utilisateur ou mot de passe invalide</div>

        <div class="success_message" id="successful_login">Vous avez été connecté avec succès!</div>

        <div class="buttons-wrapper">
            <div class="create-account-button">
                <?php GenerateButtonTertiary("S'inscrire", "create-account.php"); ?>
            </div>

            <div>
                <input type="submit" name="validation-login" value="Se connecter" class="button button-primary">
            </div>

        </div>
    </form>

    <?php GenerateFooter(); ?>
</body>