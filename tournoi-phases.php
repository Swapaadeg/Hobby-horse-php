<?php
include('function.php');

if (!isset($_GET['tournoi_id']) || !is_numeric($_GET['tournoi_id'])) {
    header('Location: tournois.php?error=invalid_id');
    exit();
}

$tournoi_id = (int)$_GET['tournoi_id'];

// Récupère les infos du tournoi
$stmt = $bdd->prepare("SELECT * FROM tournois WHERE id = :id");
$stmt->execute(['id' => $tournoi_id]);
$tournoi = $stmt->fetch();

if (!$tournoi) {
    header('Location: tournois.php?error=tournament_not_found');
    exit();
}

$pageTitle = "Phases du Tournoi";
include('head.php');
?>
<body>
    <?php include('nav.php'); ?>
    <section class="bracket__wrapper container">
        <h2>Phase du tournoi : <?= htmlspecialchars($tournoi['nom']) ?></h2>

        <?php if ($tournoi['type'] === 'elimination') {
            include('tournoi-elimination.php');
        } elseif ($tournoi['type'] === 'championnat') {
            include('table-championnat.php');
        } ?>
    </section>
    <?php include('footer.php'); ?>
</body>
</html>