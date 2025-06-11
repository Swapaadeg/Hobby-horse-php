<?php 
    include('function.php');
    if(!empty($_POST['nom']) && !empty($_POST['date'])){
        $nom = htmlspecialchars($_POST['nom']);
        $date = htmlspecialchars($_POST['date']);
        
        $request = $bdd->prepare("SELECT COUNT(*) AS nb 
                                FROM tournois 
                                WHERE nom = :nom"
                                );
        $request->execute(['nom' => $nom]);
        $data = $request->fetch();

        // On verifie qu'il n'y pas le même tournoi existant 
        if($data['nb'] >= 1){
            header('location:tournoi_create.php?error=2');
            exit();
        }else{

            // Préparation de la requête
            $request = $bdd->prepare('INSERT INTO tournois (nom,date)
                                    VALUES (:nom,:date)'
        );

            $request->execute(array(
            'nom' =>  $nom,
            'date'  =>  $date
            ));

            // Redirection vers l'accueil
            header('Location: index.php?success=3&date=' . urlencode($date));
            exit();
        }
    }
?>

<?php 
    $pageTitle = "Création d'un Tournoi";
    include('head.php'); 
?>
<body>
    <?php include('nav.php') ?>
    <h2>Création d'un Tournoi</h2>

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
    <div class="formulaire">
        <form action="tournoi-create.php" method="post">
            <label for="nom">Nom du Tournoi</label>
            <input type="text" name="nom" id="nom">
            <label for="date">Date du Tournoi</label>
            <input type="date" name="date" id="date">
            <button>Créer le Tournoi</button>
        </form>
    </div>
</body>
<?php include('footer.php')?>
</html>