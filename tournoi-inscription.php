<?php 
$pageTitle = "Inscription Tournoi";
include('function.php');

// Vérifier si le joueur est connecté
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'joueur') {
    header("Location: connexion.php");
    exit();
}

$joueur_id = $_SESSION['id'];

if (!empty($_POST['tournoi_id'])) {
    $tournoi_id = intval($_POST['tournoi_id']);

    // Vérifier que le joueur n’est pas déjà inscrit
    $check = $bdd->prepare("SELECT COUNT(*) AS nb 
                            FROM tournoi_participants
                            WHERE tournoi_id = :tournoi_id 
                            AND joueur_id = :joueur_id"
                            );
    $check->execute([
        'tournoi_id' => $tournoi_id,
        'joueur_id' => $joueur_id
    ]);
    $exist = $check->fetch();

    if ($exist['nb'] >= 1) {
        header("Location: tournoi-inscription.php?error=1"); // déjà inscrit
        exit();
    }

    // Inscription du joueur
    $stmt = $bdd->prepare("INSERT INTO tournoi_participants (tournoi_id, joueur_id) 
                            VALUES (:tournoi_id, :joueur_id)");
    $stmt->execute([
        'tournoi_id' => $tournoi_id,
        'joueur_id' => $joueur_id
    ]);

    header("Location: tournoi-inscription.php?success=1");
    exit();
}

?>

<?php include('head.php');?>
<?php include('nav.php');?>

<h2>Inscription à un tournoi</h2>

<?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
    <p class="success">Vous êtes inscrit au tournoi avec succès !</p>
<?php elseif (isset($_GET['error']) && $_GET['error'] == 1): ?>
    <p class="error">Vous êtes déjà inscrit à ce tournoi.</p>
<?php endif; ?>
<body>
    <form method="POST" action="tournoi-inscription.php">
        <label for="tournoi_id">Choisissez un tournoi :</label>
        <select name="tournoi_id" id="tournoi_id" required>
            <option value="" disabled selected>-- Sélectionner un tournoi --</option>
            <?php
            // Récupérer les tournois auxquels le joueur n'est pas encore inscrit
            $query = $bdd->prepare("
                SELECT tournois.id, tournois.nom 
                FROM tournois
                WHERE tournois.id NOT IN (
                                        SELECT tournoi_participants.tournoi_id 
                                        FROM tournoi_participants 
                                        WHERE tournoi_participants.joueur_id = :joueur_id
                                        )
                ");
                $query->execute(['joueur_id' => $joueur_id]);
                $tournois = $query->fetchAll();

                if (count($tournois) > 0) {
                    foreach ($tournois as $tournoi) {
                        echo "<option value='{$tournoi['id']}'>{$tournoi['nom']}</option>";
                    }
                } else {
                    echo "<option disabled>Aucun tournoi disponible</option>";
                }
            ?>
        </select>
        <button type="submit">M'inscrire au tournoi</button>
    </form>
    <?php include('footer.php')?>
</body>