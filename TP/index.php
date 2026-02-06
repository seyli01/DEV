<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TP PHP - Gestion des Étudiants</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1, h2 {
            color: #333;
        }
        form {
            margin-bottom: 30px;
        }
        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }
        input, select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 15px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .etudiant-card {
            background-color: #f9f9f9;
            padding: 15px;
            margin: 10px 0;
            border-left: 4px solid #007bff;
            border-radius: 4px;
        }
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .btn-delete {
            background-color: #dc3545;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
            font-size: 14px;
        }
        .btn-delete:hover {
            background-color: #c82333;
        }
        .btn-edit {
            background-color: #28a745;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
            margin-right: 5px;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
        }
        .btn-edit:hover {
            background-color: #218838;
        }
        dialog {
            border: none;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 520px;
        }
        dialog::backdrop {
            background: rgba(0, 0, 0, 0.4);
        }
        .dialog-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 15px;
        }
        .btn-cancel {
            background-color: #6c757d;
        }
        .btn-cancel:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📚 TP PHP - Gestion des Étudiants BTS SIO</h1>

<?php
// Démarrer la session
session_start();

// Connexion à la base de données
require_once 'db.php';

// Importer la logique métier
require_once 'logic.php';

$etudiant_a_modifier = null;
if (!empty($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $etudiant_a_modifier = get_etudiants_by_id($id);
}

// ========== TRAITEMENT DU FORMULAIRE ==========

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    //modifier 

    if (isset($_POST['action']) && $_POST['action'] === 'modifier') {
        $id = intval($_POST['id']);
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $age = $_POST['age'];
        $classe = $_POST['classe'];

        $erreurs = validerFormulaire($nom, $prenom, $age, $classe);
        if (empty($erreurs)) {
            if (update_student($id, $nom, $prenom, $age, $classe)) {
                $_SESSION['message'] = "<div class='message success'>Étudiant modifié avec succès !</div>";
            } else {
                $_SESSION['message'] = "<div class='message error'>Erreur lors de la modification.</div>";
            }
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $_SESSION['message'] = "<div class='message error'>";
            foreach ($erreurs as $erreur) {
                $_SESSION['message'] .= $erreur . "<br>";
            }
            $_SESSION['message'] .= "</div>";
        }

    //supprimer 
    } elseif (isset($_POST['action']) && $_POST['action'] === 'supprimer') {
        $id = intval($_POST['id']);
        if (delete_student($id)) {
            $_SESSION['message'] = "<div class='message success'>Étudiant supprimé avec succès !</div>";
        } else {
            $_SESSION['message'] = "<div class='message error'>Erreur lors de la suppression.</div>";
        }
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $age = $_POST['age'];
        $classe = $_POST['classe'];
        
        $erreurs = validerFormulaire($nom, $prenom, $age, $classe);
        
        if (empty($erreurs)) {
            add_students($nom, $prenom, $age, $classe);
            $_SESSION['message'] = "<div class='message success'>Étudiant ajouté avec succès !</div>";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $_SESSION['message'] = "<div class='message error'>";
            foreach ($erreurs as $erreur) {
                $_SESSION['message'] .= $erreur . "<br>";
            }
            $_SESSION['message'] .= "</div>";
        }
    }
}

$message = "";
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

echo $message;

?>

        <!-- FORMULAIRE D'AJOUT -->
        <h2>Ajouter un Étudiant</h2>
        <form method="POST" action="">
            
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" required>
            
            <label for="prenom">Prénom :</label>
            <input type="text" id="prenom" name="prenom" required>
            
            <label for="age">Âge :</label>
            <input type="number" id="age" name="age" min="15" max="30" required>
            
            <label for="classe">Classe :</label>
            <select id="classe" name="classe" required>
                <option value="">-- Choisir --</option>
                <option value="BTS SIO SISR">BTS SIO SISR</option>
                <option value="BTS SIO SLAM">BTS SIO SLAM</option>
            </select>
            
            <button type="submit">Ajouter l'étudiant</button>
        </form>

        <dialog id="editDialog">
            <h2>Modifier un Étudiant</h2>
            <form method="POST" action="" id="editForm">
                <input type="hidden" name="action" value="modifier">
                <input type="hidden" name="id" id="edit-id" value="<?php echo $etudiant_a_modifier ? (int) $etudiant_a_modifier['id'] : ''; ?>">

                <label for="edit-nom">Nom :</label>
                <input type="text" id="edit-nom" name="nom" required value="<?php echo $etudiant_a_modifier ? $etudiant_a_modifier['nom'] : ''; ?>">

                <label for="edit-prenom">Prénom :</label>
                <input type="text" id="edit-prenom" name="prenom" required value="<?php echo $etudiant_a_modifier ? $etudiant_a_modifier['prenom'] : ''; ?>">

                <label for="edit-age">Âge :</label>
                <input type="number" id="edit-age" name="age" min="15" max="30" required value="<?php echo $etudiant_a_modifier ? (int) $etudiant_a_modifier['age'] : ''; ?>">

                <label for="edit-classe">Classe :</label>
                <select id="edit-classe" name="classe" required>
                    <option value="">-- Choisir --</option>
                    <option value="BTS SIO SISR" <?php echo ($etudiant_a_modifier && $etudiant_a_modifier['classe'] === 'BTS SIO SISR') ? 'selected' : ''; ?>>BTS SIO SISR</option>
                    <option value="BTS SIO SLAM" <?php echo ($etudiant_a_modifier && $etudiant_a_modifier['classe'] === 'BTS SIO SLAM') ? 'selected' : ''; ?>>BTS SIO SLAM</option>
                </select>

                <div class="dialog-actions">
                    <button type="button" class="btn-cancel" id="cancelEdit">Annuler</button>
                    <button type="submit">Enregistrer</button>
                </div>
            </form>
        </dialog>

        <hr>

        <!-- TODO: AFFICHAGE DES STATISTIQUES -->
        <!-- Utiliser vos fonctions pour afficher:
             - Le nombre total d'étudiants
             - L'âge moyen
             - La répartition par classe
        -->
             <!-- AFFICHAGE DES STATISTIQUES -->
        <h2>Statistiques</h2>

        <?php
        $nombreTotal = count_students();
        $ageMoyen = calculate_average_age();
        $repartition = count_by_class();
        ?>

        <div class="etudiant-card">
            <p><strong>Nombre total d'étudiants :</strong> <?php echo $nombreTotal; ?></p>
            <p><strong>Âge moyen :</strong> <?php echo $ageMoyen; ?> ans</p>
            <p><strong>Répartition par classe :</strong></p>
            <ul>
                <?php
                foreach ($repartition as $classe => $nombre) {
                    echo "<li>$classe : $nombre étudiant(s)</li>";
                }
                ?>
            </ul>
        </div>

        <hr>

        <!-- TODO: AFFICHAGE DES ÉTUDIANTS -->
        <!-- Appeler votre fonction afficherEtudiants() ici -->
        <h2>Liste des Étudiants</h2>
        <?php display_students(); 
        
        ?>
    </div>
    <script>
        const dialog = document.getElementById('editDialog');
        const cancelBtn = document.getElementById('cancelEdit');

        if (dialog && dialog.showModal && <?php echo $etudiant_a_modifier ? 'true' : 'false'; ?>) {
            dialog.showModal();
        }

        cancelBtn.addEventListener('click', () => {
            if (dialog && dialog.close) {
                dialog.close();
            }
            window.location.href = 'index.php';
        });
    </script>
</body>
</html>
