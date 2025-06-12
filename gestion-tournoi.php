<?php
$pageTitle = "Gestion du Tournoi";
include('function.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php?error=access_denied');
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: tournois.php?error=invalid_id');
    exit();
}

$tournoi_id = (int)$_GET['id'];

$request = $bdd->prepare("SELECT nom, type 
                        FROM tournois 
                        WHERE id = :id"
                        );
$request->execute(['id' => $tournoi_id]);
$tournoi = $request->fetch();

if (!$tournoi) {
    header('Location: tournois.php?error=tournament_not_found');
    exit();
}

// Check le nombre de participants
$request = $bdd->prepare("SELECT COUNT(*) AS nb FROM tournoi_participants 
                                                WHERE tournoi_id = :tournoi_id"
                                                );
$request->execute(['tournoi_id' => $tournoi_id]);
$participant_count = $request->fetch()['nb'];

if ($tournoi['type'] === 'elimination' && $participant_count != 8) {
    $error = "Un tournoi par élimination doit avoir exactement 8 participants. Actuellement : $participant_count.";
} elseif ($tournoi['type'] === 'championnat' && ($participant_count < 8 || $participant_count > 12)) {
    $error = "Un tournoi de championnat doit avoir entre 8 et 12 participants. Actuellement : $participant_count.";
}

// Générer les matchs pour un tournoi par élimination
if (isset($_POST['generate_matches']) && $tournoi['type'] === 'elimination' && $participant_count == 8) {
    $request = $bdd->prepare("SELECT joueur_id 
                            FROM tournoi_participants 
                            WHERE tournoi_id = :tournoi_id"
                            );
    $request->execute(['tournoi_id' => $tournoi_id]);
    $participants = $request->fetchAll(PDO::FETCH_COLUMN);

    // random pour les participants pour les matchs
    shuffle($participants); 
    $matches = [
        [0, 1, 'quart'], [2, 3, 'quart'], [4, 5, 'quart'], [6, 7, 'quart']
    ];

    foreach ($matches as $match) {
        $stmt = $bdd->prepare("INSERT INTO matchs (tournoi_id, joueur_id_1, joueur_id_2, phase, statut) 
                            VALUES (:tournoi_id, :joueur_id_1, :joueur_id_2, :phase, 'en_attente')"
                            );
        $stmt->execute([
            'tournoi_id' => $tournoi_id,
            'joueur_id_1' => $participants[$match[0]],
            'joueur_id_2' => $participants[$match[1]],
            'phase' => $match[2]
        ]);
    }
    header("Location: gestion-tournoi.php?id=$tournoi_id&success=1");
    exit();
}

// Permet de faire les matchs pour un type championnat
if (isset($_POST['generate_matches']) && $tournoi['type'] === 'championnat') {
    $request = $bdd->prepare("SELECT joueur_id 
                            FROM tournoi_participants 
                            WHERE tournoi_id = :tournoi_id"
                            );
    $request->execute(['tournoi_id' => $tournoi_id]);
    $participants = $request->fetchAll(PDO::FETCH_COLUMN);

    for ($i = 0; $i < count($participants); $i++) {
        for ($j = $i + 1; $j < count($participants); $j++) {
            $stmt = $bdd->prepare("INSERT INTO matchs (tournoi_id, joueur_id_1, joueur_id_2, phase, statut) 
                                VALUES (:tournoi_id, :joueur_id_1, :joueur_id_2, 'championnat', 'en_attente')"
                                );
            $stmt->execute([
                'tournoi_id' => $tournoi_id,
                'joueur_id_1' => $participants[$i],
                'joueur_id_2' => $participants[$j],
                'phase' => 'championnat'
            ]);
        }
    }
    header("Location: gestion-tournoi.php?id=$tournoi_id&success=1");
    exit();
}

// Mise à jour les scores d’un match
if (!empty($_POST['match_id']) && !empty($_POST['score_joueur_1']) && !empty($_POST['score_joueur_2'])) {
    $match_id = intval($_POST['match_id']);
    $score_joueur_1 = intval($_POST['score_joueur_1']);
    $score_joueur_2 = intval($_POST['score_joueur_2']);
    $vainqueur_id = $score_joueur_1 > $score_joueur_2 ? $_POST['joueur_id_1'] : $_POST['joueur_id_2'];

    $stmt = $bdd->prepare("UPDATE matchs 
                        SET score_joueur_1 = :score_joueur_1, score_joueur_2 = :score_joueur_2, vainqueur_id = :vainqueur_id, statut = 'termine' 
                        WHERE id = :match_id"
                        );
    $stmt->execute([
        'score_joueur_1' => $score_joueur_1,
        'score_joueur_2' => $score_joueur_2,
        'vainqueur_id' => $vainqueur_id,
        'match_id' => $match_id
    ]);

    // Pour  type limination, créer les matchs des demi-finales et finale
    if ($tournoi['type'] === 'elimination') {
        $request = $bdd->prepare("SELECT COUNT(*) AS nb 
                                FROM matchs 
                                WHERE tournoi_id = :tournoi_id 
                                AND phase = 'quart' 
                                AND statut = 'termine'"
                                );
        $request->execute(['tournoi_id' => $tournoi_id]);
        if ($request->fetch()['nb'] == 4) {
            $request = $bdd->prepare("SELECT vainqueur_id 
                                    FROM matchs 
                                    WHERE tournoi_id = :tournoi_id 
                                    AND phase = 'quart' 
                                    AND statut = 'termine'"
                                    );
            $request->execute(['tournoi_id' => $tournoi_id]);
            $vainqueurs = $request->fetchAll(PDO::FETCH_COLUMN);

            $matches = [
                [0, 1, 'demi'], [2, 3, 'demi']
            ];
            foreach ($matches as $match) {
                $stmt = $bdd->prepare("INSERT INTO matchs (tournoi_id, joueur_id_1, joueur_id_2, phase, statut) 
                                    VALUES (:tournoi_id, :joueur_id_1, :joueur_id_2, :phase, 'en_attente')"
                                    );
                $stmt->execute([
                    'tournoi_id' => $tournoi_id,
                    'joueur_id_1' => $vainqueurs[$match[0]],
                    'joueur_id_2' => $vainqueurs[$match[1]],
                    'phase' => $match[2]
                ]);
            }
        }

        $request = $bdd->prepare("SELECT COUNT(*) AS nb 
                                FROM matchs 
                                WHERE tournoi_id = :tournoi_id 
                                AND phase = 'demi' 
                                AND statut = 'termine'"
                                );
        $request->execute(['tournoi_id' => $tournoi_id]);
        if ($request->fetch()['nb'] == 2) {
            $request = $bdd->prepare("SELECT vainqueur_id 
                                    FROM matchs 
                                    WHERE tournoi_id = :tournoi_id 
                                    AND phase = 'demi' 
                                    AND statut = 'termine'"
                                    );
            $request->execute(['tournoi_id' => $tournoi_id]);
            $vainqueurs = $request->fetchAll(PDO::FETCH_COLUMN);

            $stmt = $bdd->prepare("INSERT INTO matchs (tournoi_id, joueur_id_1, joueur_id_2, phase, statut) 
                                VALUES (:tournoi_id, :joueur_id_1, :joueur_id_2, 'finale', 'en_attente')"
                                );
            $stmt->execute([
                'tournoi_id' => $tournoi_id,
                'joueur_id_1' => $vainqueurs[0],
                'joueur_id_2' => $vainqueurs[1],
                'phase' => 'finale'
            ]);
        }
    }

    // classement pour championnat
    if ($tournoi['type'] === 'championnat') {
        $request = $bdd->prepare("SELECT joueur_id_1, joueur_id_2, vainqueur_id 
                                FROM matchs 
                                WHERE tournoi_id = :tournoi_id 
                                AND statut = 'termine'"
                                );
        $request->execute(['tournoi_id' => $tournoi_id]);
        $matchs = $request->fetchAll();

        $points = [];
        foreach ($matchs as $match) {
            if ($match['vainqueur_id'] == $match['joueur_id_1']) {
                $points[$match['joueur_id_1']] = ($points[$match['joueur_id_1']] ?? 0) + 3;
                $points[$match['joueur_id_2']] = ($points[$match['joueur_id_2']] ?? 0);
            } else {
                $points[$match['joueur_id_2']] = ($points[$match['joueur_id_2']] ?? 0) + 3;
                $points[$match['joueur_id_1']] = ($points[$match['joueur_id_1']] ?? 0);
            }
        }

        foreach ($points as $joueur_id => $point) {
            $stmt = $bdd->prepare("INSERT INTO classements (tournoi_id, joueur_id, points) 
                                VALUES (:tournoi_id, :joueur_id, :points) 
                                ON DUPLICATE 
                                KEY UPDATE points = :points");
            $stmt->execute([
                'tournoi_id' => $tournoi_id,
                'joueur_id' => $joueur_id,
                'points' => $point
            ]);
        }
    }

    header("Location: gestion-tournoi.php?id=$tournoi_id&success=2");
    exit();
}
?>

<?php include('head.php');?>
<body>
    <?php include('nav.php');?>
    <section class="gestion-tournoi__wrapper container">
        <h2>Gestion du Tournoi : <?php echo htmlspecialchars($tournoi['nom']); ?></h2>

        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php elseif (isset($_GET['success']) && $_GET['success'] == 1): ?>
            <p class="success">Matchs générés avec succès !</p>
        <?php elseif (isset($_GET['success']) && $_GET['success'] == 2): ?>
            <p class="success">Match mis à jour avec succès !</p>
        <?php endif; ?>

        <?php if (!isset($error)): ?>
            <form method="POST">
                <button type="submit" name="generate_matches">Générer les matchs</button>
            </form>
        <?php endif; ?>

        <h3>Matchs</h3>
        <?php
        $request = $bdd->prepare("SELECT m.*, u1.nom_utilisateur AS joueur_1, u2.nom_utilisateur AS joueur_2
                                FROM matchs m
                                JOIN utilisateurs u1 ON m.joueur_id_1 = u1.id
                                JOIN utilisateurs u2 ON m.joueur_id_2 = u2.id
                                WHERE m.tournoi_id = :tournoi_id");
        $request->execute(['tournoi_id' => $tournoi_id]);
        $matchs = $request->fetchAll();

        foreach ($matchs as $match) {
            echo '<div class="match-card">';
            echo '<p>' . htmlspecialchars($match['joueur_1']) . ' vs ' . htmlspecialchars($match['joueur_2']) . ' (' . $match['phase'] . ')</p>';
            if ($match['statut'] === 'termine') {
                echo '<p>Score : ' . $match['score_joueur_1'] . ' - ' . $match['score_joueur_2'] . '</p>';
            } else {
                echo '<form method="POST">';
                echo '<input type="hidden" name="match_id" value="' . $match['id'] . '">';
                echo '<input type="hidden" name="joueur_id_1" value="' . $match['joueur_id_1'] . '">';
                echo '<input type="hidden" name="joueur_id_2" value="' . $match['joueur_id_2'] . '">';
                echo '<input type="number" name="score_joueur_1" placeholder="Score ' . htmlspecialchars($match['joueur_1']) . '" required>';
                echo '<input type="number" name="score_joueur_2" placeholder="Score ' . htmlspecialchars($match['joueur_2']) . '" required>';
                echo '<button type="submit">Enregistrer</button>';
                echo '</form>';
            }
            echo '</div>';
        }
        ?>

        <?php if ($tournoi['type'] === 'championnat'): ?>
            <h3>Classement</h3>
            <?php
            $request = $bdd->prepare("SELECT c.points, u.nom_utilisateur
                                    FROM classements c
                                    JOIN utilisateurs u ON c.joueur_id = u.id
                                    WHERE c.tournoi_id = :tournoi_id
                                    ORDER BY c.points DESC");
            $request->execute(['tournoi_id' => $tournoi_id]);
            $classements = $request->fetchAll();

            echo '<table>';
            echo '<tr><th>Joueur</th><th>Points</th></tr>';
            foreach ($classements as $classement) {
                echo '<tr><td>' . htmlspecialchars($classement['nom_utilisateur']) . '</td><td>' . $classement['points'] . '</td></tr>';
            }
            echo '</table>';
            ?>
        <?php endif; ?>
    </section>
    <?php include('footer.php')?>
</body>
</html>