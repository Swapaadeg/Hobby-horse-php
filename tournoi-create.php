<?php 
include('function.php');

// Vérifie si admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php?error=access_denied');
    exit();
}

if (!empty($_POST['nom']) && !empty($_POST['date']) && !empty($_POST['type'])) {
    $nom = htmlspecialchars($_POST['nom']);
    $date = htmlspecialchars($_POST['date']);
    $description = htmlspecialchars($_POST['description']);
    $type = in_array($_POST['type'], ['elimination', 'championnat']) ? $_POST['type'] : 'elimination';

    $request = $bdd->prepare("SELECT COUNT(*) AS nb 
                            FROM tournois 
                            WHERE nom = :nom"
                            );
    $request->execute(['nom' => $nom]);
    $data = $request->fetch();

    if ($data['nb'] >= 1) {
        header('Location: tournoi-create.php?error=2');
        exit();
    } else {
        $request = $bdd->prepare('INSERT INTO tournois (nom, date, description, type) 
                                VALUES (:nom, :date, :description, :type)'
                                );
        $request->execute([
            'nom' => $nom,
            'date' => $date,
            'description' => $description,
            'type' => $type
        ]);
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
    <section class="create-tournament__wrapper container">
        <?php include('nav.php') ?>
        <h2>Création d'un Tournoi</h2>

        <?php if (isset($_GET['error'])) { ?>
            <?php switch ($_GET['error']) {
                case 2:
                    echo "<p class='error'>Ce nom de tournoi existe déjà</p>";
                    break;
            } ?>
        <?php } ?>
        <div class="formulaire">
            <form id="auth"action="tournoi-create.php" method="post">
                <label for="nom">Nom du Tournoi</label>
                <input type="text" name="nom" id="nom" required>
                <label for="date">Date du Tournoi</label>
                <input type="date" name="date" id="date" required>
                <label for="type">Type de Tournoi</label>
                <select name="type" id="type" required>
                    <option value="elimination">Élimination (8 participants)</option>
                    <option value="championnat">Championnat (8 à 12 participants)</option>
                </select>
                <label for="description">Description</label>
                <textarea id="description" name="description" required></textarea>
                <button class="button-form">Créer le Tournoi</button>
            </form>
        </div>
    </section>
    <?php include('footer.php')?>
</body>
</html>