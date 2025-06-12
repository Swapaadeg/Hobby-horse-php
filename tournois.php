<?php 
$pageTitle = "Tournois";
include('function.php');
?>
<?php include('head.php');?>

<body>
    <?php include('nav.php');?>
    <section class="tournaments__wrapper container">
        <h2>Nos tournois</h2>
        <div class="tournaments">
            <?php
                $request = $bdd->query('SELECT *
                                        FROM tournois
                                        ORDER BY tournois.date ASC');
                while ($data = $request->fetch()) {
                    echo '<div class="tournament-card">';

                    echo '<div class="image-container">';
                    $image = !empty($data['img']) ? htmlspecialchars($data['img']) : 'Compet.webp';
                    echo '<img src="assets/img/' . $image . '" alt="Image du tournoi : ' . htmlspecialchars($data['nom']) . '">';
                    echo '</div>';

                    echo '<h3>' . ucfirst($data['nom']) . '</h3>';
                    echo '<p class="tournament-date"><strong>Date :</strong> ' . date('d/m/Y', strtotime($data['date'])) . '</p>';
                    echo '<p class="tournament-desc">' . nl2br(htmlspecialchars($data['description'])) . '</p>';
                    // echo '<p class="tournament-statut">' . htmlspecialchars($data['statut']) . '</p>';
                    echo '<div class="btn-card">';
                    echo '<a class="btn" href="tournoi-modifier.php?id=' . $data['id'] . '">Modifier</a>';
                    echo '<a class="btn" href="tournoi-supprimer.php?id=' . $data['id'] . '" onclick="return confirm(\'Êtes-vous sûr de vouloir supprimer ce tournoi 🐎 ?\')">Supprimer</a>';
                    echo '</div>';
                    echo '</div>';
                }
            ?>
        </div>
    </section>
</body>