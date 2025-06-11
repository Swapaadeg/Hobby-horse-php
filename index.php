<?php 
$pageTitle = "Tournoi Hobby Horse";
include('function.php');?>
<?php include('head.php');?>

<?php include('nav.php'); ?>
<header>
    <h1>Nos Tournois</h1>
</header>

<body>
    <!-- Bienvenue -->
    <?php if (isset($_SESSION['nom_utilisateur'])): ?>
        <h3>Bonjour <?= ucfirst(htmlspecialchars($_SESSION['nom_utilisateur'])) ?> !</h3>
    <?php endif; ?>
    <!-- SUCCESS  -->
    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <p class="success">Inscription réussie ! Vous pouvez maintenant vous connecter.</p>
    <?php endif; ?>

    <!-- AFFICHAGE DATE -->
   <?php
    if (isset($_GET['success']) && $_GET['success'] == 3) {
        $dateInput = isset($_GET['date']) ? $_GET['date'] : '';
        if (!empty($dateInput)) {
            $date = new DateTime($dateInput);
            $formatter = new IntlDateFormatter(
                'fr_FR',
                IntlDateFormatter::FULL,
                IntlDateFormatter::NONE
            );
            $formatter->setPattern('EEEE dd MMMM YYYY'); 
            // DATE FRANCAIS
            $frenchDate = 'le ' . ucfirst(strtolower($formatter->format($date)));
        } else {
            $frenchDate = 'une date non spécifiée';
        }
        ?>
        <p class="success">Votre Tournoi est bien créé et débutera <?php echo $frenchDate; ?>.</p>
        <?php } ?>

</body>