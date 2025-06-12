<?php
$stmt = $bdd->prepare("
    SELECT c.points, c.position, u.nom_utilisateur
    FROM classements c
    JOIN utilisateurs u ON c.joueur_id = u.id
    WHERE c.tournoi_id = :tournoi_id
    ORDER BY c.position ASC, c.points DESC
");
$stmt->execute(['tournoi_id' => $tournoi_id]);
$classements = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<table class="champion-table">
    <thead>
        <tr><th>Position</th><th>Joueur</th><th>Points</th></tr>
    </thead>
    <tbody>
        <?php foreach ($classements as $c): ?>
        <tr>
            <td><?= $c['position'] ?: '-' ?></td>
            <td><?= htmlspecialchars($c['nom_utilisateur']) ?></td>
            <td><?= $c['points'] ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>