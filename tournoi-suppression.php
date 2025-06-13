<?php
    include('function.php');

    // Vérification admin
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header('Location: index.php?error=access_denied');
        exit();
    }

    // Vérifie qu'on a un ID de tournoi
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $tournoi_id = (int)$_GET['id'];

        // Supprimer les dépendances : matchs, participants, classements
        $deleteMatchs = $bdd->prepare("DELETE FROM matchs 
                                    WHERE tournoi_id = :id"
                                    );
        $deleteMatchs->execute(['id' => $tournoi_id]);

        $deleteParticipants = $bdd->prepare("DELETE FROM tournoi_participants 
                                            WHERE tournoi_id = :id"
                                            );
        $deleteParticipants->execute(['id' => $tournoi_id]);

        $deleteClassements = $bdd->prepare("DELETE FROM classements 
                                            WHERE tournoi_id = :id"
                                            );
        $deleteClassements->execute(['id' => $tournoi_id]);

        // Supprimer le tournoi
        $deleteTournoi = $bdd->prepare("DELETE FROM tournois 
                                        WHERE id = :id"
                                        );
        $deleteTournoi->execute(['id' => $tournoi_id]);

        // Redirection avec succès
        header('Location: tournois.php?success=suppression');
        exit();
    } else {
        header('Location: tournois.php?error=invalid_id');
        exit();
    }
?>