<?php
    include('function.php');

    if(!empty($_POST['nom_utilisateur']) && !empty($_POST['mot_de_passe'])){
        $entryNom= htmlspecialchars($_POST['nom_utilisateur']);
        $entrymot_de_passe =($_POST['mot_de_passe']);

        $request = $bdd->prepare("SELECT *
                                FROM utilisateurs
                                WHERE nom_utilisateur = :nom_utilisateur"
                                );
        $request->execute(['nom_utilisateur' => $entryNom]);
        $data = $request->fetch();

        if($data && password_verify($entrymot_de_passe, $data['mot_de_passe'])) {
            $_SESSION['nom_utilisateur'] = $data['nom_utilisateur'];
            $_SESSION['id'] = $data['id'];
            $_SESSION['role'] = $data['role'];

             // Si la case "se souvenir de moi est coché
            if (!empty($_POST['souvenir'])) {
                $token = bin2hex(random_bytes(32));

                // Sauvegarder le token en base
                $stmt = $bdd->prepare("UPDATE utilisateurs 
                                    SET token = :token 
                                    WHERE id = :id"
                                    );
                $stmt->execute([
                    'token' => $token,
                    'id' => $data['id']
                ]);

                // Créer un cookie pour 30 jours
                setcookie('auth_token', $token, time() + (86400 * 7), '/', '', false, true);
            }
            
            header('location: index.php');
            exit();
        }else{
            echo '<p class=error">Entrées incorrectes</p>';
        }
    }
?>

<?php 
    $pageTitle = "Connexion";
    include('head.php'); 
?>
<body>
    <?php include('nav.php') ?>
    <h2>Connexion</h2>
    <div class="formulaire__wrapper container">
        <form id="auth" action="connexion.php" method='POST'>
            <label for="nom_utilisateur">Votre Pseudo</label>
            <input type="text" name="nom_utilisateur" id="nom_utilisateur">
            <label for="mot_de_passe">Votre mot de passe</label>
            <input type="password" name="mot_de_passe" id="mot_de_passe">
            <label>
                <input type="checkbox" name="souvenir"> Se souvenir de moi
            </label>
            <button>Me connecter</button>
        </form> 
    </div>
    <?php include('footer.php')?>
</body>
</html>