<?php
include('function.php');

// Restreindre l'accès aux admi
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php?error=access_denied');
    exit();
}

// Vérifier l'id du tournoi 
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: tournois.php?error=invalid_id');
    exit();
}

$tournament_id = (int)$_GET['id'];

// Récupérer les détails du tournoi
$request = $bdd->prepare("SELECT nom, date, description
                        FROM tournois 
                        WHERE id = :id"
                        );
$request->execute(['id' => $tournament_id]);
$tournament = $request->fetch(PDO::FETCH_ASSOC);

if (!$tournament) {
    header('Location: tournois.php?error=tournament_not_found');
    exit();
}

// formulaire pour la modification
if (!empty($_POST['nom']) && !empty($_POST['date'])) {
    $nom = htmlspecialchars($_POST['nom'], ENT_QUOTES, 'UTF-8');
    $date = htmlspecialchars($_POST['date'], ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars($_POST['description'], ENT_QUOTES, 'UTF-8');
    // Vérifier si un autre tournoi avec le même nom existe 
    $request = $bdd->prepare("SELECT COUNT(*) AS nb 
                            FROM tournois 
                            WHERE nom = :nom 
                            AND id != :id"
                            );
    $request->execute(['nom' => $nom, 'id' => $tournament_id]);
    $data = $request->fetch(PDO::FETCH_ASSOC);

    if ($data['nb'] >= 1) {
        header('Location: tournoi-modifier.php?id=' . $tournament_id . '&error=2');
        exit();
    } else {
        // Mise à jour du tournoi
        $request = $bdd->prepare('UPDATE tournois SET nom = :nom, date = :date, description = :description WHERE id = :id');
        $request->execute([
            'nom' => $nom,
            'date' => $date,
            'description' => $description,
            'id' => $tournament_id
        ]);

        // direction vers la liste des tournois
        header('Location: tournois.php?success=4');
        exit();
    }
}
?>

<?php 
$pageTitle = "Modifier un Tournoi";
include('head.php'); 
?>
<body>
    <section class="modifier__wrapper container">
        <?php include('nav.php') ?>
        <h2>Modifier un Tournoi</h2>

        <?php if (isset($_GET['error'])) { ?>
            <?php switch ($_GET['error']) {
                case 1:
                    echo "<p class='error'>Une erreur inattendue s'est produite</p>";
                    break;
                case 2:
                    echo "<p class='error'>Ce nom de tournoi existe déjà</p>";
                    break;
                case 'access_denied':
                    echo "<p class='error'>Accès refusé : vous devez être administrateur</p>";
                    break;
                case 'invalid_id':
                    echo "<p class='error'>Identifiant de tournoi invalide</p>";
                    break;
                case 'tournament_not_found':
                    echo "<p class='error'>Tournoi non trouvé</p>";
                    break;
            } ?>
        <?php } ?>
        <div class="formulaire">
            <form action="tournoi-modifier.php?id=<?php echo $tournament_id; ?>" method="post">
                <label for="nom">Nom du Tournoi</label>
                <input type="text" name="nom" id="nom" value="<?php echo htmlspecialchars($tournament['nom'], ENT_QUOTES, 'UTF-8'); ?>" required>
                <label for="date">Date du Tournoi</label>
                <input type="date" name="date" id="date" value="<?php echo htmlspecialchars($tournament['date'], ENT_QUOTES, 'UTF-8'); ?>" required>
                <label for="description">Description</label>
                <textarea name="description" id="description" required><?php echo htmlspecialchars($tournament['description'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                <button>Modifier le Tournoi</button>
            </form>
        </div>
    </section>
    <?php include('footer.php') ?>
</body>
</html>