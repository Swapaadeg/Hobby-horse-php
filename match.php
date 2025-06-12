<?php
    $pageTitle = "Gestion des Matchs";
    include('function.php');

    // Vérifier si l'utilisateur est admin
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header('Location: index.php?error=access_denied');
        exit();
    }

    // Vérifier l'id du tournoi
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        header('Location: tournois.php?error=invalid_id');
        exit();
    }

    $tournoi_id = (int)$_GET['id'];

    // Récupérer les détails du tournoi
    $request = $bdd->prepare("SELECT nom, type FROM tournois WHERE id = :id");
    $request->execute(['id' => $tournoi_id]);
    $tournoi = $request->fetch();

    if (!$tournoi) {
        header('Location: tournois.php?error=tournament_not_found');
        exit();
    }

    // Vérifier le nombre de participants
    $request = $bdd->prepare("SELECT COUNT(*) AS nb FROM tournoi_participants WHERE tournoi_id = :tournoi_id");
    $request->execute(['tournoi_id' => $tournoi_id]);
    $participant_count = $request->fetch()['nb'];

    $error = null;
    if ($tournoi['type'] === 'elimination' && $participant_count != 8) {
        $error = "Un tournoi par élimination doit avoir exactement 8 participants. Actuellement : $participant_count.";
    } elseif ($tournoi['type'] === 'championnat' && ($participant_count < 8 || $participant_count > 12)) {
        $error = "Un tournoi de championnat doit avoir entre 8 et 12 participants. Actuellement : $participant_count.";
    }

    // Générer les matchs si demandé
    if (isset($_POST['generate_matches']) && !$error) {
        $request = $bdd->prepare("SELECT joueur_id FROM tournoi_participants WHERE tournoi_id = :tournoi_id");
        $request->execute(['tournoi_id' => $tournoi_id]);
        $participants = $request->fetchAll(PDO::FETCH_COLUMN);

        if ($tournoi['type'] === 'elimination' && $participant_count == 8) {
            shuffle($participants);
            $matches = [
                [0, 1, 'quart'], [2, 3, 'quart'], [4, 5, 'quart'], [6, 7, 'quart']
            ];

            foreach ($matches as $match) {
                $stmt = $bdd->prepare("INSERT INTO matchs (tournoi_id, joueur1_id, joueur2_id, phase, statut, date) VALUES (:tournoi_id, :joueur1_id, :joueur2_id, :phase, 'A venir', NOW())");
                $stmt->execute([
                    'tournoi_id' => $tournoi_id,
                    'joueur1_id' => $participants[$match[0]],
                    'joueur2_id' => $participants[$match[1]],
                    'phase' => $match[2]
                ]);
            }
        } elseif ($tournoi['type'] === 'championnat') {
            for ($i = 0; $i < count($participants); $i++) {
                for ($j = $i + 1; $j < count($participants); $j++) {
                    $stmt = $bdd->prepare("INSERT INTO matchs (tournoi_id, joueur1_id, joueur2_id, phase, statut, date) VALUES (:tournoi_id, :joueur1_id, :joueur2_id, :phase, 'A venir', NOW())");
                    $stmt->execute([
                        'tournoi_id' => $tournoi_id,
                        'joueur1_id' => $participants[$i],
                        'joueur2_id' => $participants[$j],
                        'phase' => 'championnat'
                    ]);

                }
            }
        }
        header("Location: match.php?id=$tournoi_id&success=1");
        exit();
    }

    // Mettre à jour les scores d’un match
    if (!empty($_POST['match_id']) && isset($_POST['score_joueur1']) && isset($_POST['score_joueur2'])) {
        $match_id = intval($_POST['match_id']);
        $score_joueur1 = intval($_POST['score_joueur1']);
        $score_joueur2 = intval($_POST['score_joueur2']);
        $vainqueur_id = $score_joueur1 > $score_joueur2 ? $_POST['joueur1_id'] : $_POST['joueur2_id'];

        $stmt = $bdd->prepare("UPDATE matchs SET score_joueur1 = :score_joueur1, score_joueur2 = :score_joueur2, vainqueur_id = :vainqueur_id, statut = 'Terminé' WHERE id = :match_id");
        $stmt->execute([
            'score_joueur1' => $score_joueur1,
            'score_joueur2' => $score_joueur2,
            'vainqueur_id' => $vainqueur_id,
            'match_id' => $match_id
        ]);

        // Progression pour élimination
        if ($tournoi['type'] === 'elimination') {
            // Vérifier si tous les quarts sont terminés
            $request = $bdd->prepare("SELECT COUNT(*) AS nb FROM matchs WHERE tournoi_id = :tournoi_id AND phase = 'quart' AND statut = 'Terminé'");
            $request->execute(['tournoi_id' => $tournoi_id]);
            if ($request->fetch()['nb'] == 4) {
                $request = $bdd->prepare("SELECT vainqueur_id FROM matchs WHERE tournoi_id = :tournoi_id AND phase = 'quart' AND statut = 'Terminé'");
                $request->execute(['tournoi_id' => $tournoi_id]);
                $vainqueurs = $request->fetchAll(PDO::FETCH_COLUMN);

                $matches = [
                    [0, 1, 'demi'], [2, 3, 'demi']
                ];
                foreach ($matches as $match) {
                    $stmt = $bdd->prepare("INSERT INTO matchs (tournoi_id, joueur1_id, joueur2_id, phase, statut, date) VALUES (:tournoi_id, :joueur1_id, :joueur2_id, :phase, 'A venir', NOW())");
                    $stmt->execute([
                        'tournoi_id' => $tournoi_id,
                        'joueur1_id' => $vainqueurs[$match[0]],
                        'joueur2_id' => $vainqueurs[$match[1]],
                        'phase' => $match[2]
                    ]);
                }
            }

            // Vérifier si toutes les demi-finales sont terminées
            $request = $bdd->prepare("SELECT COUNT(*) AS nb FROM matchs WHERE tournoi_id = :tournoi_id AND phase = 'demi' AND statut = 'Terminé'");
            $request->execute(['tournoi_id' => $tournoi_id]);
            if ($request->fetch()['nb'] == 2) {
                $request = $bdd->prepare("SELECT vainqueur_id FROM matchs WHERE tournoi_id = :tournoi_id AND phase = 'demi' AND statut = 'Terminé'");
                $request->execute(['tournoi_id' => $tournoi_id]);
                $vainqueurs = $request->fetchAll(PDO::FETCH_COLUMN);

                $stmt = $bdd->prepare("INSERT INTO matchs (tournoi_id, joueur1_id, joueur2_id, phase, statut, date) VALUES (:tournoi_id, :joueur1_id, :joueur2_id, 'finale', 'A venir', NOW())");
                $stmt->execute([
                    'tournoi_id' => $tournoi_id,
                    'joueur1_id' => $vainqueurs[0],
                    'joueur2_id' => $vainqueurs[1],
                    'phase' => 'finale'
                ]);
            }

            // Mettre à jour le classement pour la finale
            $request = $bdd->prepare("SELECT COUNT(*) AS nb FROM matchs WHERE tournoi_id = :tournoi_id AND phase = 'finale' AND statut = 'Terminé'");
            $request->execute(['tournoi_id' => $tournoi_id]);
            if ($request->fetch()['nb'] == 1) {
                $request = $bdd->prepare("SELECT joueur1_id, joueur2_id, vainqueur_id FROM matchs WHERE tournoi_id = :tournoi_id AND phase = 'finale' AND statut = 'Terminé'");
                $request->execute(['tournoi_id' => $tournoi_id]);
                $finale = $request->fetch();

                $stmt = $bdd->prepare("INSERT INTO classements (tournoi_id, joueur_id, position, points) VALUES (:tournoi_id, :joueur_id, :position, :points)");
                $stmt->execute([
                    'tournoi_id' => $tournoi_id,
                    'joueur_id' => $finale['vainqueur_id'],
                    'position' => 1,
                    'points' => 3
                ]);
                $loser_id = $finale['vainqueur_id'] == $finale['joueur1_id'] ? $finale['joueur2_id'] : $finale['joueur1_id'];
                $stmt->execute([
                    'tournoi_id' => $tournoi_id,
                    'joueur_id' => $loser_id,
                    'position' => 2,
                    'points' => 1
                ]);
            }
        }

        // Mettre à jour le classement pour championnat
        if ($tournoi['type'] === 'championnat') {
            $request = $bdd->prepare("SELECT joueur1_id, joueur2_id, vainqueur_id FROM matchs WHERE tournoi_id = :tournoi_id AND statut = 'Terminé'");
            $request->execute(['tournoi_id' => $tournoi_id]);
            $matchs = $request->fetchAll();

            $points = [];
            foreach ($matchs as $match) {
                if ($match['vainqueur_id'] == $match['joueur1_id']) {
                    $points[$match['joueur1_id']] = ($points[$match['joueur1_id']] ?? 0) + 3;
                    $points[$match['joueur2_id']] = ($points[$match['joueur2_id']] ?? 0);
                } elseif ($match['vainqueur_id'] == $match['joueur2_id']) {
                    $points[$match['joueur2_id']] = ($points[$match['joueur2_id']] ?? 0) + 3;
                    $points[$match['joueur1_id']] = ($points[$match['joueur1_id']] ?? 0);
                }
            }

            foreach ($points as $joueur_id => $point) {
                $stmt = $bdd->prepare("INSERT INTO classements (tournoi_id, joueur_id, points) VALUES (:tournoi_id, :joueur_id, :points) ON DUPLICATE KEY UPDATE points = :points");
                $stmt->execute([
                    'tournoi_id' => $tournoi_id,
                    'joueur_id' => $joueur_id,
                    'points' => $point
                ]);
            }

            // Mettre à jour les positions après tous les matchs
            $request = $bdd->prepare("SELECT COUNT(*) AS nb FROM matchs WHERE tournoi_id = :tournoi_id AND statut != 'Terminé'");
            $request->execute(['tournoi_id' => $tournoi_id]);
            if ($request->fetch()['nb'] == 0) {
                $request = $bdd->prepare("SELECT joueur_id, points FROM classements WHERE tournoi_id = :tournoi_id ORDER BY points DESC");
                $request->execute(['tournoi_id' => $tournoi_id]);
                $classements = $request->fetchAll();

                foreach ($classements as $index => $classement) {
                    $stmt = $bdd->prepare("UPDATE classements SET position = :position WHERE tournoi_id = :tournoi_id AND joueur_id = :joueur_id");
                    $stmt->execute([
                        'position' => $index + 1,
                        'tournoi_id' => $tournoi_id,
                        'joueur_id' => $classement['joueur_id']
                    ]);
                }
            }
        }

        header("Location: match.php?id=$tournoi_id&success=2");
        exit();
    }
?>

<?php include('head.php'); ?>
<body>
    <?php include('nav.php'); ?>
    <section class="match__wrapper container">
        <h2>Gestion des Matchs : <?php echo htmlspecialchars($tournoi['nom']); ?></h2>

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
        $request = $bdd->prepare("
            SELECT m.*, u1.nom_utilisateur AS joueur_1, u2.nom_utilisateur AS joueur_2
            FROM matchs m
            JOIN utilisateurs u1 ON m.joueur1_id = u1.id
            JOIN utilisateurs u2 ON m.joueur2_id = u2.id
            WHERE m.tournoi_id = :tournoi_id
            ORDER BY m.phase, m.id
        ");
        $request->execute(['tournoi_id' => $tournoi_id]);
        $matchs = $request->fetchAll();

        if (count($matchs) > 0) {
            echo '<div class="match-list">';
            foreach ($matchs as $match) {
                echo '<div class="match-card">';
                echo '<p>' . htmlspecialchars($match['joueur_1']) . ' vs ' . htmlspecialchars($match['joueur_2']) . ' (' . ucfirst($match['phase']) . ')</p>';
                echo '<p>Statut : ' . htmlspecialchars($match['statut']) . '</p>';
                if ($match['statut'] === 'Terminé') {
                    echo '<p>Score : ' . $match['score_joueur1'] . ' - ' . $match['score_joueur2'] . '</p>';
                } else {
                    echo '<form method="POST">';
                    echo '<input type="hidden" name="match_id" value="' . $match['id'] . '">';
                    echo '<input type="hidden" name="joueur1_id" value="' . $match['joueur1_id'] . '">';
                    echo '<input type="hidden" name="joueur2_id" value="' . $match['joueur2_id'] . '">';
                    echo '<input type="number" name="score_joueur1" placeholder="Score ' . htmlspecialchars($match['joueur_1']) . '" required>';
                    echo '<input type="number" name="score_joueur2" placeholder="Score ' . htmlspecialchars($match['joueur_2']) . '" required>';
                    echo '<button type="submit">Enregistrer</button>';
                    echo '</form>';
                }
                echo '</div>';
            }
            echo '</div>';
        } else {
            echo '<p>Aucun match n\'a été généré pour ce tournoi.</p>';
        }
        ?>

        <?php if ($tournoi['type'] === 'championnat'): ?>
            <h3>Classement</h3>
            <?php
            $request = $bdd->prepare("
                SELECT c.points, c.position, u.nom_utilisateur
                FROM classements c
                JOIN utilisateurs u ON c.joueur_id = u.id
                WHERE c.tournoi_id = :tournoi_id
                ORDER BY c.position ASC, c.points DESC
            ");
            $request->execute(['tournoi_id' => $tournoi_id]);
            $classements = $request->fetchAll();

            if (count($classements) > 0) {
                echo '<table>';
                echo '<tr><th>Position</th><th>Joueur</th><th>Points</th></tr>';
                foreach ($classements as $classement) {
                    echo '<tr><td>' . ($classement['position'] ?: '-') . '</td><td>' . htmlspecialchars($classement['nom_utilisateur']) . '</td><td>' . $classement['points'] . '</td></tr>';
                }
                echo '</table>';
            } else {
                echo '<p>Aucun classement disponible pour le moment.</p>';
            }
            ?>
        <?php elseif ($tournoi['type'] === 'elimination'): ?>
            <h3>Classement</h3>
            <?php
            $request = $bdd->prepare("
                SELECT c.position, c.points, u.nom_utilisateur
                FROM classements c
                JOIN utilisateurs u ON c.joueur_id = u.id
                WHERE c.tournoi_id = :tournoi_id
                ORDER BY c.position ASC
            ");
            $request->execute(['tournoi_id' => $tournoi_id]);
            $classements = $request->fetchAll();

            if (count($classements) > 0) {
                echo '<table>';
                echo '<tr><th>Position</th><th>Joueur</th><th>Points</th></tr>';
                foreach ($classements as $classement) {
                    echo '<tr><td>' . $classement['position'] . '</td><td>' . htmlspecialchars($classement['nom_utilisateur']) . '</td><td>' . $classement['points'] . '</td></tr>';
                }
                echo '</table>';
            } else {
                echo '<p>Aucun classement disponible pour le moment.</p>';
            }
            ?>
        <?php endif; ?>
    </section>
    <?php include('footer.php') ?>
</body>
</html>