<?php
session_start();

include "fonctions.php";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Insertion</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }

        .section {
            display: flex;
            gap: 40px;
            margin-bottom: 60px;
        }

        .formulaire, .tableau {
            width: 50%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #aaa;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #eee;
        }
    </style>
</head>
<body>

<header>
    <h1>DVDThèque - Insertion</h1>
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

<article>
        <?php

        // Sécurité : accès admin uniquement
        if (!isset($_SESSION['statut']) || $_SESSION['statut'] !== 'admin') {
            echo "<p style='color:red;'>Accès refusé. Veuillez vous connecter en tant qu'administrateur pour accéder à cette page.</p>";
            echo "<p> Redirection dans quelques secondes ... </p>";
            redirect("index.php",1);
            exit();
        }

        // Connexion à la base MariaDB
        try {
            $db = new PDO("mysql:host=" . DB_HOST . ";dbname=dvdtheque;charset=utf8mb4", DB_USER, DB_PASS);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
        // Insertion d'un film
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["ajouter_film"])) {
            $titre = trim($_POST["titre"]);
            $date = $_POST["date_sortie"];
            $genre = $_POST["genre"];
            $nom_realisateur = trim($_POST["nom_realisateur"]);
            $prenom_realisateur = trim($_POST["prenom_realisateur"]);

            if ($titre && $date && $genre && $nom_realisateur && $prenom_realisateur) {
                // Vérifie si le réalisateur existe
                $stmt = $db->prepare("SELECT NoRealisateur FROM Realisateur WHERE Nom = ? AND Prenom = ?");
                $stmt->execute([$nom_realisateur, $prenom_realisateur]);
                $realisateur = $stmt->fetch();

                if ($realisateur) {
                    $id_realisateur = $realisateur["NoRealisateur"];
                } else {
                    // Si le réalisateur n'existe pas, l'insérer
                    $stmt = $db->prepare("INSERT INTO Realisateur (Nom, Prenom) VALUES (?, ?)");
                    $stmt->execute([$nom_realisateur, $prenom_realisateur]);
                    $id_realisateur = $db->lastInsertId();
                }

                // Insertion du film
                $stmt = $db->prepare("INSERT INTO Film (Titre, DateSortie, Genre, NoRealisateur) VALUES (?, ?, ?, ?)");
                $stmt->execute([$titre, $date, $genre, $id_realisateur]);

                echo "<p class='success'>Film ajouté avec succès.</p>";
            } else {
                echo "<p class='error'>Veuillez remplir tous les champs.</p>";
            }
        }

        // Insertion d’un réalisateur seul
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["ajouter_realisateur"])) {
            $nom = trim($_POST["nom"]);
            $prenom = trim($_POST["prenom"]);
            $pays = trim($_POST["pays"]);

            if ($nom && $prenom) {
                $stmt = $db->prepare("INSERT INTO Realisateur (Nom, Prenom,Pays) VALUES (?, ?, ?)");
                $stmt->execute([$nom, $prenom,$pays]);
                echo "<p class='success'>Réalisateur ajouté avec succès.</p>";
            } else {
                echo "<p class='error'>Veuillez renseigner nom et prénom.</p>";
            }
        }
        ?>

        <!-- SECTION : Ajout Film -->
        <div class="section">
            <div class="formulaire">
                <h2>Ajouter un film</h2>
                <form method="POST">
                    <input type="hidden" name="ajouter_film" value="1">
                    <label>Titre :</label><br>
                    <input type="text" name="titre" required><br><br>

                    <label>Date de sortie :</label><br>
                    <input type="date" name="date_sortie" required><br><br>

                    <label>Genre :</label><br>
                    <input type="text" name="genre" required><br><br>

                    <label>Nom du réalisateur :</label><br>
                    <input type="text" name="nom_realisateur" required><br><br>

                    <label>Prénom du réalisateur :</label><br>
                    <input type="text" name="prenom_realisateur" required><br><br>

                    <button type="submit">Ajouter le film</button>
                </form>
            </div>

            <div class="tableau">
                <h2>Liste des films</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Date</th>
                            <th>Genre</th>
                            <th>Réalisateur</th>
                            <th>id_realisateur</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $films = $db->query("
                        SELECT f.Titre, f.DateSortie, f.Genre, r.Nom, r.Prenom, r.NoRealisateur
                        FROM Film f
                        LEFT JOIN Realisateur r ON f.NoRealisateur = r.NoRealisateur
                        
                    ");
                    foreach ($films as $film) {
                        echo "<tr>
                            <td>" . htmlspecialchars($film['Titre']) . "</td>
                            <td>" . htmlspecialchars($film['DateSortie']) . "</td>
                            <td>" . htmlspecialchars($film['Genre']) . "</td>
                            <td>" . htmlspecialchars($film['Prenom'] . " " . $film['Nom']) . "</td>
                            <td>" . htmlspecialchars($film['NoRealisateur']) . "</td>
                        </tr>";
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- SECTION : Ajout Réalisateur -->
        <div class="section">
            <div class="formulaire">
                <h2>Ajouter un réalisateur</h2>
                <form method="POST">
                    <input type="hidden" name="ajouter_realisateur" value="1">
                    <label>Nom :</label><br>
                    <input type="text" name="nom" required><br><br>

                    <label>Prénom :</label><br>
                    <input type="text" name="prenom" required><br><br>

                    <label>Nationalité :</label><br>
                    <input type="text" name="pays" required><br><br>

                    <button type="submit">Ajouter le réalisateur</button>
                </form>
            </div>

            <div class="tableau">
                <h2>Liste des réalisateurs</h2>
                <table>
                    <thead>
                        <tr>
                            <th>IdRea</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            
                            <th>Nationalité</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $realisateurs = $db->query("SELECT Nom, Prenom, NoRealisateur, Pays FROM Realisateur ");
                    foreach ($realisateurs as $r) {
                        echo "<tr>
                            <td>" . htmlspecialchars($r['NoRealisateur']) . "</td>
                            <td>" . htmlspecialchars($r['Nom']) . "</td>
                            <td>" . htmlspecialchars($r['Prenom']) . "</td>
                            
                            <td>" . htmlspecialchars($r['Pays']) . "</td>
                        </tr>";
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
</article>
<footer>
        <p>Application développée par Valentin PIERRE et Thomas LE FLOC'H</p>
        <p>R&T Lannion - 2025</p>
    </footer>
</body>
</html>
