<?php 
include('function.php');
if(!empty($_POST['nom_utilisateur']) && !empty($_POST['mot_de_passe'])){
    $nom_utilisateur = htmlspecialchars($_POST['nom_utilisateur']);
    $mot_de_passe = htmlspecialchars($_POST['mot_de_passe']);
    $mot_de_passeConfirm = htmlspecialchars($_POST['mot_de_passeConfirm']);

    $request = $bdd->prepare("SELECT COUNT(*) AS nb 
                            FROM utilisateurs 
                            WHERE nom_utilisateur = :nom_utilisateur"
                            );
    $request->execute(['nom_utilisateur' => $nom_utilisateur]);
    $data = $request->fetch();

    // On verifie que cet utilisateur n'existe pas déjà
    if($data['nb'] >= 1){
        header('location:inscription.php?error=2');
        exit();
    }else{

        // Cryptage du mot de passe
        if($mot_de_passe == $mot_de_passeConfirm){
            $mot_de_passeCrypt = password_hash($mot_de_passe,PASSWORD_BCRYPT);

        if ($mot_de_passe !== $mot_de_passeConfirm) {
        header('Location: inscription.php?error=1');
        exit();
    }
            // Préparation de la requête
            $request = $bdd->prepare('INSERT INTO utilisateurs (nom_utilisateur,mot_de_passe, role)
                                    VALUES (:nom_utilisateur,:mot_de_passe, :role)'
        );

            $request->execute(array(
            'nom_utilisateur' =>  $nom_utilisateur,
            'mot_de_passe'  =>  $mot_de_passeCrypt,
            'role' => 'joueur'
            ));
            
            // Redirection vers l'accueil
            header('Location: index.php?success=1');
            exit();
        }
    }
}

?>

<?php 
$pageTitle = "Inscription";
include('head.php'); ?>
<body>
    <?php include('nav.php') ?>
    <h2>Inscription</h2>
    
    <?php if(isset($_GET['error'])){ ?>
       <?php  switch($_GET['error']){
                case 1:
                    echo "<p class='error'>Vos mots de passe ne correspondent pas</p>";
                    break;
                case 2:
                    echo "<p class='error'>Ce nom d'utilisateur existe déjà</p>";
                    break;
            }
        } ?>
    <div class="formulaire__wrapper container">
        <form action="inscription.php" method="post">
            <label for="nom_utilisateur">Votre Pseudo</label>
            <input type="text" name="nom_utilisateur" id="nom_utilisateur">
            <label for="password">Votre mot de passe</label>
            <input type="password" name="mot_de_passe" id="mot_de_passe">
            <label for="passwordConfirm">Confirmez votre mot de passe</label>
            <input type="password" name="mot_de_passeConfirm" id="mot_de_passeConfirm">
            <button>S'inscrire!</button>
        </form>
    </div>    
    <?php include('footer.php')?>
</body>
</html>