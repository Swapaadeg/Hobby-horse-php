<?php
include('function.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php?error=access_denied');
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: tournois.php?error=invalid_id');
    exit();
}

$tournament_id = (int)$_GET['id'];

$request = $bdd->prepare("SELECT nom, date, description, type 
                        FROM tournois 
                        WHERE id = :id"
                        );
$request->execute(['id' => $tournament_id]);
$tournament = $request->fetch(PDO::FETCH_ASSOC);

if (!$tournament) {
    header('Location: tournois.php?error=tournament_not_found');
    exit();
}

if (!empty($_POST['nom']) && !empty($_POST['date']) && !empty($_POST['type'])) {
    $nom = htmlspecialchars($_POST['nom'], ENT_QUOTES, 'UTF-8');
    $date = htmlspecialchars($_POST['date'], ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars($_POST['description'], ENT_QUOTES, 'UTF-8');
    $type = in_array($_POST['type'], ['elimination', 'championnat']) ? $_POST['type'] : $tournament['type'];

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
        $request = $bdd->prepare('UPDATE tournois 
                                SET nom = :nom, date = :date, description = :description, type = :type 
                                WHERE id = :id'
                                );
        $request->execute([
            'nom' => $nom,
            'date' => $date,
            'description' => $description,
            'type' => $type,
            'id' => $tournament_id
        ]);
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
                    echo "<p class='error'>Ce Tournoi n'existe pas demandé à l'admin de le créer</p>";
                    break;
            } ?>
        <?php } ?>
        <div class="formulaire">
            <form id="auth" action="tournoi-modifier.php?id=<?php echo $tournament_id; ?>" method="post">
                <label for="nom">Nom du Tournoi</label>
                <input type="text" name="nom" id="nom" value="<?php echo htmlspecialchars($tournament['nom'], ENT_QUOTES, 'UTF-8'); ?>" required>
                <label for="date">Date du Tournoi</label>
                <input type="date" name="date" id="date" value="<?php echo htmlspecialchars($tournament['date'], ENT_QUOTES, 'UTF-8'); ?>" required>
                <label for="type">Type de Tournoi</label>
                <select name="type" id="type" required>
                    <option value="elimination" <?php echo $tournament['type'] === 'elimination' ? 'selected' : ''; ?>>Élimination (8 participants)</option>
                    <option value="championnat" <?php echo $tournament['type'] === 'championnat' ? 'selected' : ''; ?>>Championnat (8 à 12 participants)</option>
                </select>
                <label for="description">Description</label>
                <textarea name="description" id="description" required><?php echo htmlspecialchars($tournament['description'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                <button class="button-form">Modifier le Tournoi</button>
            </form>
        </div>
    </section>
    <?php include('footer.php') ?>
</body>
</html>