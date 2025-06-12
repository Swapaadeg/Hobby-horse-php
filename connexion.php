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
            <button>Me connecter</button>
        </form> 
    </div>
    <?php include('footer.php')?>
</body>
</html>