<?php
// Récupération des joueurs
$stmt = $bdd->prepare("
    SELECT DISTINCT u.id, u.nom_utilisateur 
    FROM tournoi_participants tp
    JOIN utilisateurs u ON tp.joueur_id = u.id
    WHERE tp.tournoi_id = :tournoi_id
    ORDER BY u.nom_utilisateur
");
$stmt->execute(['tournoi_id' => $tournoi_id]);
$joueurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Index pour accès rapide
$joueurNoms = array_column($joueurs, 'nom_utilisateur', 'id');

// Récupération des matchs
$stmt = $bdd->prepare("
    SELECT * FROM matchs 
    WHERE tournoi_id = :tournoi_id AND phase = 'championnat'
");
$stmt->execute(['tournoi_id' => $tournoi_id]);
$matchs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Organisation des matchs par joueurs
$grille = [];
foreach ($matchs as $match) {
    $grille[$match['joueur1_id']][$match['joueur2_id']] = $match;
    $grille[$match['joueur2_id']][$match['joueur1_id']] = $match; // miroir
}
?>
<body>
    <table class="champion-matrix">
        <thead>
            <tr>
                <th class="corner">Joueur</th>
                <?php foreach ($joueurs as $col): ?>
                    <th class="player-col" data-joueur-id="<?= $col['id'] ?>"><?= htmlspecialchars($col['nom_utilisateur']) ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($joueurs as $row): ?>
                <tr>
                    <th class="player-row" data-joueur-id="<?= $row['id'] ?>"><?= htmlspecialchars($row['nom_utilisateur']) ?></th>
                    <?php foreach ($joueurs as $col): ?>
                        <td class="result-cell"
                            data-joueur1="<?= $row['id'] ?>"
                            data-joueur2="<?= $col['id'] ?>">
                            <?php if ($row['id'] === $col['id']): ?>
                                <span class="self">🦄</span>
                            <?php elseif (isset($grille[$row['id']][$col['id']])): 
                                $m = $grille[$row['id']][$col['id']];
                                if ($m['statut'] === 'Terminé'): ?>
                                    <?= $m['joueur1_id'] === $row['id'] ? $m['score_joueur1'] . ' - ' . $m['score_joueur2'] : $m['score_joueur2'] . ' - ' . $m['score_joueur1'] ?>
                                <?php elseif ($m['statut'] === 'En cours'): ?>
                                    <span class="ongoing">🕒</span>
                                <?php else: ?>
                                    <span class="upcoming">📅</span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="empty">-</span>
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <script src="script.js"></script>
</body>