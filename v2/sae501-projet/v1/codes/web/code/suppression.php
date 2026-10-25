<?php
session_start();
include "fonctions.php";

// Redirection si non connecté
if (!isset($_SESSION['statut']) || $_SESSION['statut'] !== 'admin') {
            echo "<p style='color:red;'>Accès refusé. Veuillez vous connecter en tant qu'administrateur pour accéder à cette page.</p>";
            echo "<p> Redirection dans quelques secondes ... </p>";
            redirect("index.php",1);
            exit();
        }

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Suppression</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../styles.css">
    <style>

        
        .section {
            display: flex;
            gap: 2rem;
            margin-top: 2rem;
        }
        .formulaire{
            flex: 0 0 260px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .th, .td {
            border: 1px solid #aaa;
            padding: 8px;
        }
        .success { color: green; }
        .error { color: red; }

    </style>
    <script>
        function confirmerSuppression() {
            return confirm("Confirmer la suppression ?");
        }
    </script>
</head>
<body>

<header>
    <h1>DVDThèque - Suppression</h1>
    <?php
    if(!empty($_SESSION) && isset($_SESSION["mail"]) && isset($_SESSION["statut"])) {
            echo '<p>Connecté en tant que '.$_SESSION["mail"].'</p>';
            ?>
            <form method="POST" action="connexion.php">
                <button type="submit" name="deconnexion" value="true">Déconnexion</button>
                </form>

                <?php

                if ($_SESSION["statut"] == "admin") {
                    echo "<p>Statut: Administrateur <p>";
                }
                else {
                    echo "<p>Statut: Utilisateur</p>";
                }
            }
            else {
                echo "<p>Statut: Déconnecté</p>";
            }
        ?>
        <hr>
</header>

<nav>
        <?php
        if (isset($_SESSION) && isset($_SESSION["statut"])) {
            // si le statut est admin, on affiche toutes les pages
            if ($_SESSION["statut"] == "admin") {
                echo '<a href="index.php">Menu principal</a><br>';
                echo '<a href="connexion.php">Page de connexion</a><br>';
                echo '<a href="insertion.php">Insérer des DVDs</a><br>';
                echo '<a href="modification.php">Modifier des DVDs</a><br>';
                echo '<a href="suppression.php">Supprimer des DVDs</a><br>';

            }
            else {
                // sinon, seulement certaines
                echo '<a href="index.php">Menu principal</a><br>';
                echo '<a href="connexion.php">Page de connexion</a><br>';
            }
        }
        ?>
		<hr>
    </nav>

<?php

try {
    $db = new PDO('sqlite:db/DVDTheque.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}


// TRAITEMENT DES SUPPRESSIONS


// Suppression d’un film
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["supprimer_film"])) {
    $idFilm = (int)($_POST["film_id"] ?? 0);
    if ($idFilm) {
        $stmt = $db->prepare("DELETE FROM Film WHERE NoFilm = ?");
        $stmt->execute([$idFilm]);
        $msg = "<p class='success'>Film supprimé.</p>";
    } else {
        $msg = "<p class='error'>Sélection invalide pour la suppression de film.</p>";
    }
}

// Suppression d’un réalisateur
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["supprimer_realisateur"])) {
    $idRea = (int)($_POST["realisateur_id"] ?? 0);
    if ($idRea) {
        $stmt = $db->prepare("DELETE FROM Realisateur WHERE NoRealisateur = ?");
        $stmt->execute([$idRea]);
        $msg = "<p class='success'>Réalisateur supprimé.</p>";
    } else {
        $msg = "<p class='error'>Sélection invalide pour la suppression de réalisateur.</p>";
    }
}


// RÉCUPÉRATION DES DONNÉES


// Liste complète des films + réalisateur
$listeFilms = $db->query("\n    SELECT f.NoFilm, f.Titre, r.Prenom, r.Nom, strftime('%d/%m/%Y', f.DateSortie) AS DateSortie, f.Genre\n    FROM Film f\n    LEFT JOIN Realisateur r ON r.NoRealisateur = f.NoRealisateur\n    ORDER BY f.Titre ASC\n")->fetchAll(PDO::FETCH_ASSOC);

// Liste complète des réalisateurs
$listeRealisateurs = $db->query("\n    SELECT NoRealisateur, Nom, Prenom, Pays\n    FROM Realisateur\n    ORDER BY Nom, Prenom\n")->fetchAll(PDO::FETCH_ASSOC);

?>
<?php if (isset($msg)) echo $msg; ?>

<!-- ======================= FILMS ======================= -->
<div class="section">
    <div class="formulaire">
        <h2>Supprimer un film</h2>
        <form method="POST" onsubmit="return confirmerSuppression();" id="formFilm">
            <input type="hidden" name="supprimer_film" value="1">
            <select name="film_id" class="form-select" onchange="document.getElementById('formFilm').submit();">
                <option value="">-- Sélectionner un film --</option>
                <?php foreach ($listeFilms as $film): ?>
                    <option value="<?= $film['NoFilm']; ?>">
                        <?= htmlspecialchars($film['Titre'] . ' - ' . $film['Prenom'] . ' ' . $film['Nom']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <div class="tableau flex-fill">
        <h2>Liste des films</h2>
        <table class="table table-sm">
            <thead class="table-light">
                <tr>
                    <th>Titre</th>
                    <th>Date</th>
                    <th>Genre</th>
                    <th>Réalisateur</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($listeFilms as $film): ?>
                <tr>
                    <td><?= htmlspecialchars($film['Titre']); ?></td>
                    <td><?= htmlspecialchars($film['DateSortie']); ?></td>
                    <td><?= htmlspecialchars($film['Genre']); ?></td>
                    <td><?= htmlspecialchars($film['Prenom'] . ' ' . $film['Nom']); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ======================= RÉALISATEURS ======================= -->
<div class="section">
    <div class="formulaire">
        <h2>Supprimer un réalisateur</h2>
        <form method="POST" onsubmit="return confirmerSuppression();" id="formRea">
            <input type="hidden" name="supprimer_realisateur" value="1">
            <select name="realisateur_id" class="form-select" onchange="document.getElementById('formRea').submit();">
                <option value="">-- Sélectionner un réalisateur --</option>
                <?php foreach ($listeRealisateurs as $rea): ?>
                    <option value="<?= $rea['NoRealisateur']; ?>">
                        <?= htmlspecialchars($rea['Nom'] . ' ' . $rea['Prenom'] . ' - ' . $rea['Pays']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <div class="tableau flex-fill">
        <h2>Liste des réalisateurs</h2>
        <table class="table_suppr table-sm">
            <thead class="table-light">
                <tr>
                    <th>Id</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Nationalité</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($listeRealisateurs as $rea): ?>
                <tr>
                    <td><?= htmlspecialchars($rea['NoRealisateur']); ?></td>
                    <td><?= htmlspecialchars($rea['Nom']); ?></td>
                    <td><?= htmlspecialchars($rea['Prenom']); ?></td>
                    <td><?= htmlspecialchars($rea['Pays']); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<footer class="text-center mt-5">
    <p>Application développée par Valentin PIERRE et Thomas LE FLOC'H</p>
    <p>R&T Lannion - 2025</p>
</footer>
</body>
</html>
