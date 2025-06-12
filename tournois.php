<?php 
    $pageTitle = "Tournois";
    include('function.php');
?>
<?php include('head.php');?>
<?php
    if (isset($_GET['success']) && $_GET['success'] === 'suppression') {
        echo '<p class="success">Tournoi supprimé avec succès ! 🐎💨</p>';
    }
?>
<body>
    <?php include('nav.php');?>
    <section class="tournaments__wrapper container">
        <h2>Nos tournois</h2>
        <div class="tournaments">

            <?php
                $request = $bdd->query('SELECT * FROM tournois 
                                        ORDER BY tournois.date 
                                        ASC'
                                        );
                while ($data = $request->fetch()) {
                    echo '<div class="tournament-card">';

                    echo '<div class="image-container">';
                    $image = !empty($data['img']) ? htmlspecialchars($data['img']) : 'Compet.webp';
                    echo '<img src="assets/img/' . $image . '" alt="Image du tournoi : ' . htmlspecialchars($data['nom']) . '">';
                    echo '</div>';

                    echo '<h3>' . ucfirst($data['nom']) . '</h3>';
                    echo '<p class="tournament-type"><strong>Type :</strong> ' . ($data['type'] === 'elimination' ? 'Élimination' : 'Championnat') . '</p>';
                    echo '<p class="tournament-date"><strong>Date :</strong> ' . date('d/m/Y', strtotime($data['date'])) . '</p>';
                    echo '<p class="tournament-desc">' . nl2br($data['description']) . '</p>';

                    echo '<div class="btn-card">';
                    // Boutons visibles pour tous
                    echo '<a class="btn" href="classement.php?tournoi_id=' . $data['id'] . '">Voir le classement</a>';
                    echo '<a class="btn" href="tournoi-phases.php?tournoi_id=' . $data['id'] . '">Voir le tournoi</a>';
                    // Inscription : connecté = bouton, sinon = message
                    if (isset($_SESSION['id'])) {
                        echo '<a class="btn" href="tournoi-inscription.php?tournoi_id=' . $data['id'] . '">📝 S\'inscrire au tournoi</a>';
                    } else {
                        echo '<p class="info-message">🔒 Connectez-vous pour vous inscrire au tournoi</p>';
                    }

                    // Boutons supplémentaires pour admin uniquement
                    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
                        echo '<a class="btn" href="tournoi-modifier.php?id=' . $data['id'] . '">✏️ Modifier</a>';
                        echo '<a class="btn" href="tournoi-suppression.php?id=' . $data['id'] . '" onclick="return confirm(\'Êtes-vous sûr de vouloir supprimer ce tournoi 🐎 ?\')">🗑️ Supprimer</a>';
                        echo '<a class="btn" href="match.php?id=' . $data['id'] . '">🎯 Gérer les Matchs</a>';
                    }
echo '</div>';
                    echo '</div>';
                }
            ?>
        </div>
    </section>
    <?php include('footer.php')?>
</body>
</html>