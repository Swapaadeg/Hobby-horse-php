<?php 
$pageTitle = "Gestion des Joueurs";
include('function.php');

// Vérifier si l'utilisateur est admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php?error=access_denied");
    exit();
}

if (!empty($_POST['tournoi_id']) && !empty($_POST['joueur_id'])) {
    $tournoi_id = intval($_POST['tournoi_id']);
    $joueur_id = intval($_POST['joueur_id']);

    // Vérifier le type de tournoi et le nombre de participants
    $request = $bdd->prepare("SELECT type 
                            FROM tournois 
                            WHERE id = :tournoi_id"
                            );
    $request->execute(['tournoi_id' => $tournoi_id]);
    $tournoi = $request->fetch();

    if (!$tournoi) {
        header("Location: gestion-joueur.php?error=2");
        exit();
    }

    $request = $bdd->prepare("SELECT COUNT(*) AS nb 
                            FROM tournoi_participants 
                            WHERE tournoi_id = :tournoi_id"
                            );
    $request->execute(['tournoi_id' => $tournoi_id]);
    $participant_count = $request->fetch()['nb'];

    // Validation des limites
    if ($tournoi['type'] === 'elimination' && $participant_count >= 8) {
        header("Location: gestion-joueur.php?error=3");
        exit();
    } elseif ($tournoi['type'] === 'championnat' && $participant_count >= 12) {
        header("Location: gestion-joueur.php?error=3");
        exit();
    }

    // Vérifier que le joueur n’est pas déjà inscrit
    $check = $bdd->prepare("SELECT COUNT(*) AS nb 
                            FROM tournoi_participants 
                            WHERE tournoi_id = :tournoi_id 
                            AND joueur_id = :joueur_id"
                            );
    $check->execute(['tournoi_id' => $tournoi_id, 'joueur_id' => $joueur_id]);
    $exist = $check->fetch();

    if ($exist['nb'] >= 1) {
        header("Location: gestion-joueur.php?error=1");
        exit();
    }

    // Inscription du joueur
    $stmt = $bdd->prepare("INSERT INTO tournoi_participants (tournoi_id, joueur_id) 
                        VALUES (:tournoi_id, :joueur_id)"
                        );
    $stmt->execute(['tournoi_id' => $tournoi_id, 'joueur_id' => $joueur_id]);

    header("Location: gestion-joueur.php?success=1");
    exit();
}
?>

<?php include('head.php'); ?>
<body>
    <?php include('nav.php'); ?>
    <section class="gestion-joueur__wrapper container">
        <h2>Gestion des Joueurs</h2>

        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
            <p class="success">Joueur inscrit au tournoi avec succès !</p>
        <?php elseif (isset($_GET['error'])): ?>
            <?php switch ($_GET['error']) {
                case 1:
                    echo "<p class='error'>Ce joueur est déjà inscrit à ce tournoi.</p>";
                    break;
                case 2:
                    echo "<p class='error'>Tournoi non trouvé.</p>";
                    break;
                case 3:
                    echo "<p class='error'>Le nombre maximum de participants pour ce tournoi est atteint.</p>";
                    break;
                case 'access_denied':
                    echo "<p class='error'>Accès refusé : vous devez être administrateur.</p>";
                    break;
            } ?>
        <?php endif; ?>

        <form id="auth" method="POST" action="gestion-joueur.php">
            <label for="tournoi_id">Choisissez un tournoi :</label>
            <select name="tournoi_id" id="tournoi_id" required>
                <option value="" disabled selected>-- Sélectionner un tournoi --</option>
                <?php
                $query = $bdd->prepare("
                    SELECT t.id, t.nom, t.type, COUNT(tp.joueur_id) AS participant_count
                    FROM tournois t
                    LEFT JOIN tournoi_participants tp ON t.id = tp.tournoi_id
                    GROUP BY t.id
                    HAVING (t.type = 'elimination' AND participant_count < 8)
                        OR (t.type = 'championnat' AND participant_count < 12)
                ");
                $query->execute();
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

            <label for="joueur_id">Choisissez un joueur :</label>
            <select name="joueur_id" id="joueur_id" required>
                <option value="" disabled selected>-- Sélectionner un joueur --</option>
                <?php
                $query = $bdd->prepare("SELECT id, nom_utilisateur 
                                        FROM utilisateurs 
                                        WHERE role = 'joueur'"
                                        );
                $query->execute();
                $joueurs = $query->fetchAll();

                if (count($joueurs) > 0) {
                    foreach ($joueurs as $joueur) {
                        echo "<option value='{$joueur['id']}'>{$joueur['nom_utilisateur']}</option>";
                    }
                } else {
                    echo "<option disabled>Aucun joueur disponible</option>";
                }
                ?>
            </select>

            <button type="submit" class="button-form">Inscrire le joueur</button>
        </form>
    </section>
    <?php include('footer.php') ?>
</body>
</html>