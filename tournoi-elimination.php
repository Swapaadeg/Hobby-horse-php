<?php
$phases = ['quart', 'demi', 'finale'];
$bracket = [];

foreach ($phases as $phase) {
    $stmt = $bdd->prepare("SELECT m.*, u1.nom_utilisateur AS joueur1, u2.nom_utilisateur AS joueur2
                        FROM matchs m
                        JOIN utilisateurs u1 ON m.joueur1_id = u1.id
                        JOIN utilisateurs u2 ON m.joueur2_id = u2.id
                        WHERE m.tournoi_id = :id AND m.phase = :phase
                        ORDER BY m.id ASC"
                        );
    $stmt->execute([
        'id' => $tournoi_id,
        'phase' => $phase
    ]);
    $bracket[$phase] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="bracket">
    <?php foreach ($bracket as $phase => $matches): ?>
        <div class="round round-<?= htmlspecialchars($phase) ?>">
            <h4><?= ucfirst($phase) ?></h4>
            <?php foreach ($matches as $match): ?>
                <div class="match-box">
                    <span>
                        <?= htmlspecialchars($match['joueur1']) ?>
                        <?php if ($match['statut'] === 'Terminé'): ?>
                            <strong>(<?= $match['score_joueur1'] ?>)</strong>
                        <?php endif; ?>
                    </span>
                    <span>
                        <?= htmlspecialchars($match['joueur2']) ?>
                        <?php if ($match['statut'] === 'Terminé'): ?>
                            <strong>(<?= $match['score_joueur2'] ?>)</strong>
                        <?php endif; ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</div>