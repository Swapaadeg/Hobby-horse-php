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
$request = $bdd->prepare("SELECT nom, type 
                        FROM tournois 
                        WHERE id = :id");
$request->execute(['id' => $tournoi_id]);
$tournoi = $request->fetch();

if (!$tournoi) {
    header('Location: tournois.php?error=tournament_not_found');
    exit();
}

// Vérifier le nombre de participants
$request = $bdd->prepare("SELECT COUNT(*) AS nb 
                        FROM tournoi_participants 
                        WHERE tournoi_id = :tournoi_id");
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
    // Vérifier si des matchs existent déjà
    $request = $bdd->prepare("SELECT COUNT(*) AS nb 
                            FROM matchs 
                            WHERE tournoi_id = :tournoi_id");
    $request->execute(['tournoi_id' => $tournoi_id]);
    if ($request->fetch()['nb'] > 0) {
        header("Location: match.php?id=$tournoi_id&error=matches_already_generated");
        exit();
    }

    $request = $bdd->prepare("SELECT joueur_id 
                            FROM tournoi_participants 
                            WHERE tournoi_id = :tournoi_id");
    $request->execute(['tournoi_id' => $tournoi_id]);
    $participants = $request->fetchAll(PDO::FETCH_COLUMN);

    if ($tournoi['type'] === 'elimination' && $participant_count == 8) {
        shuffle($participants);
        $matches = [
            [0, 1, 'quart'], [2, 3, 'quart'], [4, 5, 'quart'], [6, 7, 'quart']
        ];

        foreach ($matches as $match) {
            $stmt = $bdd->prepare("INSERT INTO matchs (tournoi_id, joueur1_id, joueur2_id, phase, statut, date) 
                                VALUES (:tournoi_id, :joueur1_id, :joueur2_id, :phase, 'A venir', 
                                NOW())"
                                );
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
                $stmt = $bdd->prepare("INSERT INTO matchs (tournoi_id, joueur1_id, joueur2_id, phase, statut, date) 
                                    VALUES (:tournoi_id, :joueur1_id, :joueur2_id, :phase, 'A venir', 
                                    NOW())"
                                    );
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

    // Récupérer la phase du match
    $request = $bdd->prepare("SELECT phase, statut 
                            FROM matchs 
                            WHERE id = :match_id");
    $request->execute(['match_id' => $match_id]);
    $match = $request->fetch();

    // Gérer les matchs nuls
    $vainqueur_id = null;
    if ($score_joueur1 > $score_joueur2) {
        $vainqueur_id = $_POST['joueur1_id'];
    } elseif ($score_joueur2 > $score_joueur1) {
        $vainqueur_id = $_POST['joueur2_id'];
    } else {
        header("Location: match.php?id=$tournoi_id&error=draw_not_allowed");
        exit();
    }

    // Mettre à jour le match
    $stmt = $bdd->prepare("UPDATE matchs 
                            SET score_joueur1 = :score_joueur1, score_joueur2 = :score_joueur2, vainqueur_id = :vainqueur_id, statut = 'Terminé' 
                            WHERE id = :match_id"
                            );
    $stmt->execute([
        'score_joueur1' => $score_joueur1,
        'score_joueur2' => $score_joueur2,
        'vainqueur_id' => $vainqueur_id,
        'match_id' => $match_id
    ]);

    // Si le match modifié est dans un tournoi par élimination, gérer les impacts
    if ($tournoi['type'] === 'elimination') {
        // Si le match modifié est un quart, supprimer les demi-finales, la finale et le classement
        if ($match['phase'] === 'quart') {
            $stmt = $bdd->prepare("DELETE 
                                    FROM matchs 
                                    WHERE tournoi_id = :tournoi_id 
                                    AND phase IN ('demi', 'finale')"
                                    );

            $stmt->execute(['tournoi_id' => $tournoi_id]);
            $stmt = $bdd->prepare("DELETE 
                                    FROM classements 
                                    WHERE tournoi_id = :tournoi_id"
                                    );

            $stmt->execute(['tournoi_id' => $tournoi_id]);
        }
        // Si le match modifié est une demi-finale, supprimer la finale et le classement
        elseif ($match['phase'] === 'demi') {
            $stmt = $bdd->prepare("DELETE 
                                    FROM matchs 
                                    WHERE tournoi_id = :tournoi_id 
                                    AND phase = 'finale'"
                                    );

            $stmt->execute(['tournoi_id' => $tournoi_id]);
            $stmt = $bdd->prepare("DELETE 
                                    FROM classements 
                                    WHERE tournoi_id = :tournoi_id"
                                    );

            $stmt->execute(['tournoi_id' => $tournoi_id]);
        }
        // Si le match modifié est la finale, supprimer le classement
        elseif ($match['phase'] === 'finale') {
            $stmt = $bdd->prepare("DELETE 
                                    FROM classements 
                                    WHERE tournoi_id = :tournoi_id"
                                    );
                                    
            $stmt->execute(['tournoi_id' => $tournoi_id]);
        }

        // Vérifier si tous les quarts sont terminés
        $request = $bdd->prepare("SELECT COUNT(*) AS nb 
                                FROM matchs 
                                WHERE tournoi_id = :tournoi_id 
                                AND phase = 'quart' 
                                AND statut = 'Terminé'"
                                );

        $request->execute(['tournoi_id' => $tournoi_id]);
        if ($request->fetch()['nb'] == 4) {
            // Vérifier si des demi-finales existent déjà
            $request = $bdd->prepare("SELECT COUNT(*) AS nb 
                                    FROM matchs 
                                    WHERE tournoi_id = :tournoi_id 
                                    AND phase = 'demi'"
                                    );
            $request->execute(['tournoi_id' => $tournoi_id]);
            if ($request->fetch()['nb'] == 0) {
                $request = $bdd->prepare("SELECT vainqueur_id 
                                        FROM matchs 
                                        WHERE tournoi_id = :tournoi_id 
                                        AND phase = 'quart' 
                                        AND statut = 'Terminé' 
                                        ORDER BY id"
                                        );

                $request->execute(['tournoi_id' => $tournoi_id]);
                $vainqueurs = $request->fetchAll(PDO::FETCH_COLUMN);

                if (count($vainqueurs) == 4) {
                    $matches = [
                        [0, 1, 'demi'], [2, 3, 'demi']
                    ];
                    foreach ($matches as $match) {
                        $stmt = $bdd->prepare("INSERT INTO matchs (tournoi_id, joueur1_id, joueur2_id, phase, statut, date) 
                                            VALUES (:tournoi_id, :joueur1_id, :joueur2_id, :phase, 'A venir', NOW())"
                                            );
                        $stmt->execute([
                            'tournoi_id' => $tournoi_id,
                            'joueur1_id' => $vainqueurs[$match[0]],
                            'joueur2_id' => $vainqueurs[$match[1]],
                            'phase' => $match[2]
                        ]);
                    }
                }
            }
        }

        // Vérifier si toutes les demi-finales sont terminées
        $request = $bdd->prepare("SELECT COUNT(*) AS nb 
                                FROM matchs 
                                WHERE tournoi_id = :tournoi_id 
                                AND phase = 'demi' 
                                AND statut = 'Terminé'"
                                );

        $request->execute(['tournoi_id' => $tournoi_id]);
        if ($request->fetch()['nb'] == 2) {
            // Vérifier si la finale existe déjà
            $request = $bdd->prepare("SELECT COUNT(*) AS nb 
                                    FROM matchs 
                                    WHERE tournoi_id = :tournoi_id 
                                    AND phase = 'finale'"
                                    );
            $request->execute(['tournoi_id' => $tournoi_id]);
            if ($request->fetch()['nb'] == 0) {
                $request = $bdd->prepare("SELECT vainqueur_id 
                                        FROM matchs 
                                        WHERE tournoi_id = :tournoi_id 
                                        AND phase = 'demi' 
                                        AND statut = 'Terminé' 
                                        ORDER BY id"
                                        );

                $request->execute(['tournoi_id' => $tournoi_id]);
                $vainqueurs = $request->fetchAll(PDO::FETCH_COLUMN);

                if (count($vainqueurs) == 2) {
                    $stmt = $bdd->prepare("INSERT INTO matchs (tournoi_id, joueur1_id, joueur2_id, phase, statut, date) 
                                        VALUES (:tournoi_id, :joueur1_id, :joueur2_id, :phase, 'A venir', NOW())"
                                        );
                    $stmt->execute([
                        'tournoi_id' => $tournoi_id,
                        'joueur1_id' => $vainqueurs[0],
                        'joueur2_id' => $vainqueurs[1],
                        'phase' => 'finale'
                    ]);
                }
            }
        }

        // Mettre à jour le classement pour la finale
        $request = $bdd->prepare("SELECT COUNT(*) AS nb 
                                FROM matchs 
                                WHERE tournoi_id = :tournoi_id 
                                AND phase = 'finale' 
                                AND statut = 'Terminé'"
                                );

        $request->execute(['tournoi_id' => $tournoi_id]);
        if ($request->fetch()['nb'] == 1) {
            // Vérifier si le classement existe déjà
            $request = $bdd->prepare("SELECT COUNT(*) AS nb 
                                    FROM classements 
                                    WHERE tournoi_id = :tournoi_id"
                                    );

            $request->execute(['tournoi_id' => $tournoi_id]);
            if ($request->fetch()['nb'] == 0) {
                // Récupérer les détails de la finale
                $request = $bdd->prepare("SELECT joueur1_id, joueur2_id, vainqueur_id 
                                        FROM matchs 
                                        WHERE tournoi_id = :tournoi_id 
                                        AND phase = 'finale' 
                                        AND statut = 'Terminé'"
                                        );

                $request->execute(['tournoi_id' => $tournoi_id]);
                $finale = $request->fetch();
                $loser_id = $finale['vainqueur_id'] == $finale['joueur1_id'] ? $finale['joueur2_id'] : $finale['joueur1_id'];

                // Insérer le vainqueur (1er) et le perdant (2e)
                $stmt = $bdd->prepare("INSERT INTO classements (tournoi_id, joueur_id, position, points) 
                                        VALUES (:tournoi_id, :joueur_id, :position, :points)"
                                        );
                $stmt->execute([
                    'tournoi_id' => $tournoi_id,
                    'joueur_id' => $finale['vainqueur_id'],
                    'position' => 1,
                    'points' => 8
                ]);
                $stmt->execute([
                    'tournoi_id' => $tournoi_id,
                    'joueur_id' => $loser_id,
                    'position' => 2,
                    'points' => 7
                ]);

                // Récupérer les perdants des demi-finales
                $request = $bdd->prepare("
                    SELECT m.joueur1_id, m.joueur2_id, m.vainqueur_id, m.score_joueur1, m.score_joueur2 FROM matchs m
                    WHERE tournoi_id = :tournoi_id AND phase = 'demi' AND statut = 'Terminé'
                ");
                $request->execute(['tournoi_id' => $tournoi_id]);
                $demis = $request->fetchAll(PDO::FETCH_ASSOC);

                $perdants_scores = [];
                foreach ($demis as $demi) {
                    $perdant_id = $demi['vainqueur_id'] == $demi['joueur1_id'] ? $demi['joueur2_id'] : $demi['joueur1_id'];
                    $score = $demi['vainqueur_id'] == $demi['joueur1_id'] ? $demi['score_joueur2'] : $demi['score_joueur1'];
                    $perdants_scores[$perdant_id] = $score;
                }

                // Trier les perdants par score
                arsort($perdants_scores);
                $perdants = array_keys($perdants_scores);
                $scores = array_values($perdants_scores);

                // Gérer les ex æquo pour la 3e place
                if (count($perdants) == 2) {
                    if ($scores[0] === $scores[1]) {
                        // Ex æquo : 3e place pour les deux
                        $stmt->execute([
                            'tournoi_id' => $tournoi_id,
                            'joueur_id' => $perdants[0],
                            'position' => 3,
                            'points' => 6
                        ]);
                        $stmt->execute([
                            'tournoi_id' => $tournoi_id,
                            'joueur_id' => $perdants[1],
                            'position' => 3,
                            'points' => 6
                        ]);
                    } else {
                        // 3e et 4e places
                        $stmt->execute([
                            'tournoi_id' => $tournoi_id,
                            'joueur_id' => $perdants[0],
                            'position' => 3,
                            'points' => 6
                        ]);
                        $stmt->execute([
                            'tournoi_id' => $tournoi_id,
                            'joueur_id' => $perdants[1],
                            'position' => 4,
                            'points' => 5
                        ]);
                    }
                }

                // Récupérer les perdants des quarts de finale
                $request = $bdd->prepare("
                    SELECT m.joueur1_id, m.joueur2_id, m.vainqueur_id, m.score_joueur1, m.score_joueur2
                    FROM matchs m
                    WHERE tournoi_id = :tournoi_id AND phase = 'quart' AND statut = 'Terminé'
                ");
                $request->execute(['tournoi_id' => $tournoi_id]);
                $quarts = $request->fetchAll(PDO::FETCH_ASSOC);

                $perdants_quarts = [];
                foreach ($quarts as $quart) {
                    $perdant_id = $quart['vainqueur_id'] == $quart['joueur1_id'] ? $quart['joueur2_id'] : $quart['joueur1_id'];
                    $score = $quart['vainqueur_id'] == $quart['joueur1_id'] ? $quart['score_joueur2'] : $quart['score_joueur1'];
                    $perdants_quarts[$perdant_id] = $score;
                }

                // Trier les perdants des quarts par score
                arsort($perdants_quarts);
                $perdants = array_keys($perdants_quarts);

                // Insérer les perdants des quarts (5e à 8e)
                $points_quarts = [4, 3, 2, 1];
                foreach ($perdants as $index => $joueur_id) {
                    $stmt = $bdd->prepare("INSERT INTO classements (tournoi_id, joueur_id, position, points) 
                                            VALUES (:tournoi_id, :joueur_id, :position, :points)");
                    $stmt->execute([
                        'tournoi_id' => $tournoi_id,
                        'joueur_id' => $joueur_id,
                        'position' => 5 + $index,
                        'points' => $points_quarts[$index]
                    ]);
                }
            }
        }
    }

    // Mettre à jour le classement pour championnat
    if ($tournoi['type'] === 'championnat') {
        // Supprimer les classements existants pour recalculer
        $stmt = $bdd->prepare("DELETE FROM classements 
                                WHERE tournoi_id = :tournoi_id"
                                );
        $stmt->execute(['tournoi_id' => $tournoi_id]);

        $request = $bdd->prepare("SELECT joueur1_id, joueur2_id, vainqueur_id 
                                FROM matchs 
                                WHERE tournoi_id = :tournoi_id 
                                AND statut = 'Terminé'"
                                );

        $request->execute(['tournoi_id' => $tournoi_id]);
        $matchs = $request->fetchAll(PDO::FETCH_ASSOC);

        $points = [];
        foreach ($matchs as $m) {
            if ($m['vainqueur_id'] == $m['joueur1_id']) {
                $points[$m['joueur1_id']] = ($points[$m['joueur1_id']] ?? 0) + 3;
                $points[$m['joueur2_id']] = ($points[$m['joueur2_id']] ?? 0);
            } elseif ($m['vainqueur_id'] == $m['joueur2_id']) {
                $points[$m['joueur2_id']] = ($points[$m['joueur2_id']] ?? 0) + 3;
                $points[$m['joueur1_id']] = ($points[$m['joueur1_id']] ?? 0);
            }
        }

        foreach ($points as $joueur_id => $point) {
            $stmt = $bdd->prepare("INSERT INTO classements (tournoi_id, joueur_id, points) 
                                    VALUES (:tournoi_id, :joueur_id, :points)");
            $stmt->execute([
                'tournoi_id' => $tournoi_id,
                'joueur_id' => $joueur_id,
                'points' => $point
            ]);
        }

        // Mettre à jour les positions après tous les matchs
        $request = $bdd->prepare("SELECT COUNT(*) AS nb 
                                FROM matchs 
                                WHERE tournoi_id = :tournoi_id 
                                AND statut != 'Terminé'"
                                );

        $request->execute(['tournoi_id' => $tournoi_id]);
        if ($request->fetch()['nb'] == 0) {
            $request = $bdd->prepare("SELECT joueur_id, points 
                                    FROM classements 
                                    WHERE tournoi_id = :tournoi_id 
                                    ORDER BY points DESC"
                                    );

            $request->execute(['tournoi_id' => $tournoi_id]);
            $classements = $request->fetchAll(PDO::FETCH_ASSOC);

            foreach ($classements as $index => $classement) {
                $stmt = $bdd->prepare("UPDATE classements 
                                    SET position = :position 
                                    WHERE tournoi_id = :tournoi_id 
                                    AND joueur_id = :joueur_id"
                                    );

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
        <h2>Gestion des Matchs : <?php echo $tournoi['nom']; ?></h2>

        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php elseif (isset($_GET['success']) && $_GET['success'] == 1): ?>
            <p class="success">Les matchs ont été générés avec succès !</p>
        <?php elseif (isset($_GET['success']) && $_GET['success'] == 2): ?>
            <p class="success">Match mis à jour avec succès.</p>
        <?php elseif (isset($_GET['error']) && $_GET['error'] === 'matches_already_generated'): ?>
            <p class="error">Les matchs ont déjà été générés pour ce tournoi.</p>
        <?php elseif (isset($_GET['error']) && $_GET['error'] === 'draw_not_allowed'): ?>
            <p class="error">Les matchs nuls ne sont pas autorisés dans les tournois par élimination.</p>
        <?php endif; ?>

        <?php if (!isset($error)): ?>
            <form method="POST">
                <button type="submit" name="generate_matches">Gérer les matchs</button>
            </form>
        <?php endif; ?>

        <h3>Matchs</h3>
        <?php
        // Récupérer les matchs
        $request = $bdd->prepare("
            SELECT m.*, u1.nom_utilisateur AS joueur_1, u2.nom_utilisateur AS joueur_2
            FROM matchs m
            JOIN utilisateurs u1 ON m.joueur1_id = u1.id
            JOIN utilisateurs u2 ON m.joueur2_id = u2.id
            WHERE m.tournoi_id = :tournoi_id
            ORDER BY FIELD(m.phase, 'finale', 'demi', 'quart'), m.id
        ");
        $request->execute(['tournoi_id' => $tournoi_id]);
        $matchs = $request->fetchAll(PDO::FETCH_ASSOC);

        if ($tournoi['type'] === 'championnat') {
            // Récupérer les participants pour le tableau croisé
            $request = $bdd->prepare("
                SELECT u.id, u.nom_utilisateur
                FROM tournoi_participants tp
                JOIN utilisateurs u ON tp.joueur_id = u.id
                WHERE tp.tournoi_id = :tournoi_id
                ORDER BY u.nom_utilisateur
            ");
            $request->execute(['tournoi_id' => $tournoi_id]);
            $participants = $request->fetchAll(PDO::FETCH_ASSOC);

            // Créer une matrice pour les matchs
            $match_matrix = [];
            foreach ($participants as $p1) {
                foreach ($participants as $p2) {
                    $match_matrix[$p1['id']][$p2['id']] = null;
                }
            }

            foreach ($matchs as $match) {
                $match_matrix[$match['joueur1_id']][$match['joueur2_id']] = $match;
                $match_matrix[$match['joueur2_id']][$match['joueur1_id']] = $match; // Symétrie
            }

            if (count($participants) > 0) {
                echo '<div class="match-table-wrapper">';
                echo '<table class="match-table">';
                // En-tête
                echo '<tr><th></th>';
                foreach ($participants as $participant) {
                    echo '<th>' . htmlspecialchars($participant['nom_utilisateur']) . '</th>';
                }
                echo '</tr>';

                // Lignes
                foreach ($participants as $p1) {
                    echo '<tr>';
                    echo '<th>' . htmlspecialchars($p1['nom_utilisateur']) . '</th>';
                    foreach ($participants as $p2) {
                        if ($p1['id'] === $p2['id']) {
                            echo '<td class="diagonal">-</td>';
                        } elseif (isset($match_matrix[$p1['id']][$p2['id']])) {
                            $match = $match_matrix[$p1['id']][$p2['id']];
                            $is_joueur1 = $match['joueur1_id'] === $p1['id'];
                            $score1 = $is_joueur1 ? $match['score_joueur1'] : $match['score_joueur2'];
                            $score2 = $is_joueur1 ? $match['score_joueur2'] : $match['score_joueur1'];
                            $joueur1_id = $match['joueur1_id'];
                            $joueur2_id = $match['joueur2_id'];
                            $class = $match['statut'] === 'Terminé' ? 'match-terminated' : 'match-pending';

                            echo '<td class="' . $class . '">';
                            echo '<form method="POST" class="match-form">';
                            echo '<input type="hidden" name="match_id" value="' . $match['id'] . '">';
                            echo '<input type="hidden" name="joueur1_id" value="' . $joueur1_id . '">';
                            echo '<input type="hidden" name="joueur2_id" value="' . $joueur2_id . '">';
                            echo '<input type="number" name="score_joueur1" value="' . ($match['statut'] === 'Terminé' ? $score1 : '') . '" placeholder="Score" required>';
                            echo '<input type="number" name="score_joueur2" value="' . ($match['statut'] === 'Terminé' ? $score2 : '') . '" placeholder="Score" required>';
                            echo '<button type="submit">' . ($match['statut'] === 'Terminé' ? 'Modifier' : 'Enregistrer') . '</button>';
                            echo '</form>';
                            echo '</td>';
                        } else {
                            echo '<td>-</td>';
                        }
                    }
                    echo '</tr>';
                }
                echo '</table>';
                echo '</div>';
            } else {
                echo '<p>Aucun participant dans ce tournoi.</p>';
            }
        } elseif ($tournoi['type'] === 'elimination') {
            // Grouper les matchs par phase
            $phases = ['quart' => [], 'demi' => [], 'finale' => []];
            foreach ($matchs as $match) {
                $phases[$match['phase']][] = $match;
            }

            if (count($matchs) > 0) {
                echo '<div class="match-list">';
                foreach (['quart' => 'Quarts de finale', 'demi' => 'Demi-finales', 'finale' => 'Finale'] as $phase_key => $phase_name) {
                    if (!empty($phases[$phase_key])) {
                        echo '<div class="phase-section">';
                        echo '<h4>' . $phase_name . '</h4>';
                        foreach ($phases[$phase_key] as $match) {
                            $class = $match['statut'] === 'Terminé' ? 'match-terminated' : 'match-pending';
                            echo '<div class="match-card ' . $class . '">';
                            echo '<p>' . htmlspecialchars($match['joueur_1']) . ' vs ' . htmlspecialchars($match['joueur_2']) . '</p>';
                            echo '<p>Statut : ' . htmlspecialchars($match['statut']) . '</p>';
                            echo '<form method="POST" class="match-form">';
                            echo '<input type="hidden" name="match_id" value="' . $match['id'] . '">';
                            echo '<input type="hidden" name="joueur1_id" value="' . $match['joueur1_id'] . '">';
                            echo '<input type="hidden" name="joueur2_id" value="' . $match['joueur2_id'] . '">';
                            echo '<input type="number" name="score_joueur1" value="' . ($match['statut'] === 'Terminé' ? $match['score_joueur1'] : '') . '" placeholder="Score ' . htmlspecialchars($match['joueur_1']) . '" required>';
                            echo '<input type="number" name="score_joueur2" value="' . ($match['statut'] === 'Terminé' ? $match['score_joueur2'] : '') . '" placeholder="Score ' . htmlspecialchars($match['joueur_2']) . '" required>';
                            echo '<button type="submit">' . ($match['statut'] === 'Terminé' ? 'Modifier' : 'Enregistrer') . '</button>';
                            echo '</form>';
                            echo '</div>';
                        }
                        echo '</div>';
                    }
                }
                echo '</div>';
            } else {
                echo '<p>Aucun match n\'a été généré pour ce tournoi.</p>';
            }
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
            $classements = $request->fetchAll(PDO::FETCH_ASSOC);
            ?>

            <?php if (count($classements) > 0): ?>
                <div class="classement">
                    <div class="classement-round">
                        <h4>Classement Final</h4>
                        <?php foreach ($classements as $classement): ?>
                            <div class="classement-box">
                                <span class="position"><?= $classement['position'] ?: '-' ?></span>
                                <span class="joueur"><?= htmlspecialchars($classement['nom_utilisateur']) ?></span>
                                <span class="points"><?= $classement['points'] ?> pts</span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <p>Aucun classement disponible pour le moment.</p>
            <?php endif; ?>
        <?php endif; ?>
    </section>
    <?php include('footer.php') ?>
</body>
</html>