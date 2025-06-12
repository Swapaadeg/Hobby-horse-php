<?php include('function.php');?>
<?php $pageTitle = "Découvrir le Hobby Horse";?>
<?php include('head.php');?>
<body>
    <?php include('nav.php') ?>
    <main>
        <section class="decouvrir__wrapper container">
            <h2>Découvrir le Hobby Horse</h2>

            <div class="hobbyhorse-layout">
                <!-- Texte à gauche -->
                <div class="hobbyhorse-text">
                    <p>
                        Le <strong>Hobby Horse</strong>, ou <em>cheval bâton</em>, est un sport original né en Finlande qui mêle
                        <strong>équitation, danse, gymnastique et créativité</strong> — sans véritable cheval.
                    </p>

                    <h3>📅 Origine</h3>
                    <p>Apparu en <strong>Finlande dans les années 2010</strong>, ce sport a explosé grâce aux réseaux sociaux.</p>

                    <h3>🏆 Disciplines</h3>
                    <ul>
                        <li><strong>Jumping</strong> : saut d’obstacles</li>
                        <li><strong>Dressage</strong> : figures chorégraphiées</li>
                        <li><strong>Cross</strong> : parcours d'obstacles extérieurs</li>
                    </ul>

                    <h3>🧑‍⚖️ Jugement</h3>
                    <p>Les participants sont notés sur leur technique, posture, créativité et précision.</p>

                    <h3>🎉 Pourquoi ça plaît ?</h3>
                    <p>Accessible, fun et original. Idéal pour s’exprimer, sans cheval réel 🐴</p>
                </div>

                <!-- Vidéos à droite -->
                <div class="hobbyhorse-videos">
                    <div class="video">
                        <iframe
                            src="https://www.youtube.com/embed/PrR0LRANrAk?autoplay=1&mute=1&loop=1&playlist=PrR0LRANrAk"
                            title="Hobby Horse Short"
                            frameborder="0"
                            allow="autoplay; encrypted-media"
                            allowfullscreen>
                        </iframe>
                    </div>
                    <div class="video">
                        <iframe
                            src="https://www.youtube.com/embed/LlheTc1eeLE?start=106"
                            title="Hobby Horse Compétition"
                            frameborder="0"
                            allow="encrypted-media"
                            allowfullscreen>
                        </iframe>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <?php include('footer.php'); ?>
</body>