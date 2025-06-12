<?php 
$pageTitle = "Inscription Tournoi";
include('function.php');

if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'joueur') {
    header("Location: connexion.php");
    exit();
}

$joueur_id = $_SESSION['id'];

if (!empty($_POST['tournoi_id'])) {
    $tournoi_id = intval($_POST['tournoi_id']);

    // Vérifier le type et le nombre de participants pour le tournoi
    $request = $bdd->prepare("SELECT type 
                            FROM tournois 
                            WHERE id = :tournoi_id"
                            );
    $request->execute(['tournoi_id' => $tournoi_id]);
    $tournoi = $request->fetch();

    if (!$tournoi) {
        header("Location: tournoi-inscription.php?error=2");
        exit();
    }

    $request = $bdd->prepare("SELECT COUNT(*) AS nb 
                            FROM tournoi_participants 
                            WHERE tournoi_id = :tournoi_id"
                            );
    $request->execute(['tournoi_id' => $tournoi_id]);
    $participant_count = $request->fetch()['nb'];

    // Validation les participants max
    if ($tournoi['type'] === 'elimination' && $participant_count >= 8) {
        header("Location: tournoi-inscription.php?error=3");
        exit();
    } elseif ($tournoi['type'] === 'championnat' && $participant_count >= 12) {
        header("Location: tournoi-inscription.php?error=3");
        exit();
    }

    // Vérifier que le joueur pas déjà inscrit
    $check = $bdd->prepare("SELECT COUNT(*) AS nb 
                        FROM tournoi_participants 
                        WHERE tournoi_id = :tournoi_id 
                        AND joueur_id = :joueur_id"
                        );
    $check->execute(['tournoi_id' => $tournoi_id, 'joueur_id' => $joueur_id]);
    $exist = $check->fetch();

    if ($exist['nb'] >= 1) {
        header("Location: tournoi-inscription.php?error=1");
        exit();
    }

    // Inscription du joueur
    $stmt = $bdd->prepare("INSERT INTO tournoi_participants (tournoi_id, joueur_id) 
                        VALUES (:tournoi_id, :joueur_id)"
                        );
    $stmt->execute(['tournoi_id' => $tournoi_id, 'joueur_id' => $joueur_id]);

    header("Location: tournoi-inscription.php?success=1");
    exit();
}
?>

<?php include('head.php');?>
<?php include('nav.php');?>

<h2>Inscription à un tournoi</h2>

<?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
    <p class="success">Vous êtes inscrit au tournoi avec succès !</p>
<?php elseif (isset($_GET['error'])): ?>
    <?php switch ($_GET['error']) {
        case 1:
            echo "<p class='error'>Vous êtes déjà inscrit à ce tournoi.</p>";
            break;
        case 2:
            echo "<p class='error'>Tournoi non trouvé.</p>";
            break;
        case 3:
            echo "<p class='error'>Le nombre maximum de participants pour ce tournoi est atteint.</p>";
            break;
    } ?>
<?php endif; ?>
<body>
    <section class="tournoi-inscription__wrapper container">
        <form method="POST" action="tournoi-inscription.php">
            <label for="tournoi_id">Choisissez un tournoi :</label>
            <select name="tournoi_id" id="tournoi_id" required>
                <option value="" disabled selected>-- Sélectionner un tournoi --</option>
                <?php
                $query = $bdd->prepare("
                    SELECT t.id, t.nom, t.type, COUNT(tp.joueur_id) AS participant_count
                    FROM tournois t
                    LEFT JOIN tournoi_participants tp ON t.id = tp.tournoi_id
                    WHERE t.id NOT IN (
                        SELECT tournoi_id 
                        FROM tournoi_participants 
                        WHERE joueur_id = :joueur_id
                    )
                    GROUP BY t.id
                    HAVING (t.type = 'elimination' AND participant_count < 8)
                        OR (t.type = 'championnat' AND participant_count < 12)
                ");
                $query->execute(['joueur_id' => $joueur_id]);
                $tournois = $query->fetchAll();

                if (count($tournois) > 0) {
                    foreach ($tournois as $tournoi) {
                        $type_label = $tournoi['type'] === 'elimination' ? 'Élimination' : 'Championnat';
                        echo "<option value='{$tournoi['id']}'>{$tournoi['nom']} ({$type_label})</option>";
                    }
                } else {
                    echo "<option disabled>Aucun tournoi disponible</option>";
                }
                ?>
            </select>
            <button type="submit">M'inscrire au tournoi</button>
        </form>
    </section>
    <?php include('footer.php')?>
</body>