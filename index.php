<?php 
$pageTitle = "Canasson Cup";
include('function.php');?>
<?php include('head.php');?>

<body>
    <?php include('nav.php'); ?>
    <header>
        <div class="title">
            <img src="assets/img/unicorn.png" alt="Licorne">
            <h1>Canasson Cup</h1>
            <img src="assets/img/unicorn.png" alt="Licorne">
        </div>
        <h3>« Le seul sport où tu transpires avec un manche à balai. »</h3>
    </header>
    <main>
        <!-- BANNER -->
         <section class="banner">
            <video id="background-video" autoplay loop muted>
                <source src="assets/video/unicorn.mp4" type="video/mp4">
            </video>
        </section>
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
    </main>
    <?php include('footer.php')?>
</body>