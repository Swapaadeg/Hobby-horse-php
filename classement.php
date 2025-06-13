<?php
$pageTitle = "Classement du Tournoi";
include('function.php');


// Vérifier l'id du tournoi
if (!isset($_GET['tournoi_id']) || !is_numeric($_GET['tournoi_id'])) {
    header('Location: tournois.php?error=invalid_id');
    exit();
}

$tournoi_id = (int)$_GET['tournoi_id'];

// Récupérer les détails du tournoi
$request = $bdd->prepare("SELECT nom, type FROM tournois WHERE id = :id");
$request->execute(['id' => $tournoi_id]);
$tournoi = $request->fetch();

if (!$tournoi) {
    header('Location: tournois.php?error=tournament_not_found');
    exit();
}

// Récupérer le classement
$request = $bdd->prepare("
    SELECT c.position, c.points, u.nom_utilisateur
    FROM classements c
    JOIN utilisateurs u ON c.joueur_id = u.id
    WHERE c.tournoi_id = :tournoi_id
    ORDER BY c.position ASC
");
$request->execute(['tournoi_id' => $tournoi_id]);
$classements = $request->fetchAll(PDO::FETCH_ASSOC);

?>

<?php include('head.php'); ?>
<body>
    <?php include('nav.php'); ?>
    <section class="classement__wrapper container">
        <h2>Classement : <?php echo $tournoi['nom']; ?></h2>

        <?php if (isset($_GET['error']) && $_GET['error'] === 'tournament_not_found'): ?>
            <p class="error">Tournoi non trouvé.</p>
        <?php endif; ?>

        <div class="classement">
            <?php if (count($classements) > 0): ?>
                <div class="classement-round">
                    <h4>Classement Final</h4>
                    <?php foreach ($classements as $classement): ?>
                        <div class="classement-box">
                            <span class="position"><?php echo $classement['position'] ?: '-'; ?></span>
                            <span class="joueur"><?php echo htmlspecialchars($classement['nom_utilisateur']); ?></span>
                            <span class="points"><?php echo $classement['points']; ?> pts</span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>Aucun classement disponible pour ce tournoi.</p>
            <?php endif; ?>
        </div>
    </section>
    <?php include('footer.php'); ?>
</body>
</html>