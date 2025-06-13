<?php
// nav.php
?>
<nav>    
    <div class="navbar">
        <div class="burger">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <ul class="menu">
            <li><a href="index.php"><img src="assets/img/logo-unicorn.png" alt=""></a></li>
            <li><a href="decouvrir.php">Découvrir le hobby horse</a></li>
            <li><a href="tournois.php">Nos tournois</a></li>
            <?php if (isset($_SESSION['id'])): ?>
                <li><a href="tournoi-inscription.php">Inscription à un tournoi</a></li>
            <?php endif; ?>
        </ul>

        <ul class="auth">
            <?php if (!isset($_SESSION['id'])): ?>
                <li><a href="inscription.php">S'inscrire</a></li>
                <li><a href="connexion.php">Se connecter</a></li>
            <?php else: ?>
                <li><a href="deconnexion.php">Se déconnecter</a></li>
            <?php endif; ?>
        </ul>

        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <ul class="admin-dropdown">
                <li class="dropdown">
                    <a href="#">Panel admin ▾</a>
                    <ul class="dropdown-menu">
                        <li><a href="tournoi-create.php">Créer un Tournoi</a></li>
                        <li><a href="gestion-joueur.php">Gérer les joueurs</a></li>
                        <li><a href="match.php">Gérer les matchs</a></li>
                    </ul>
                </li>
            </ul>
        <?php endif; ?>
    </div>
    <script src="script.js"></script>
</nav>