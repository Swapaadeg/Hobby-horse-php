<nav>    
    <!-- <input type="checkbox" id="menu-toggle">
    <label for="menu-toggle" class="menu-icon">&#9776;</label> -->
    <div class="navbar">
        <ul class="menu">
            <li><a href="index.php">Accueil</a></li>
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

        <ul>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <li><a href="tournoi-create.php">Création d'un Tournoi</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>