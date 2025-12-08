<?php 

if(!isset($_GET['id'])) {
    header('Location: children_list.php?page=1');
    exit;
}

    $id = (int)$_GET['id'];
    require_once __DIR__ . "/../../config.php";
    requireLogin();
    start_page("Modifier un pensionnaire");

    $pdo = getPDO();
    $sql = "SELECT * FROM child WHERE id_child = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);

    $child = $stmt->fetch(PDO::FETCH_ASSOC);

    // Si l'id n'existe plus, la rechercer d'un enfant dans la base de donnée retourne false
    // alors

    if(!$child) {
        header('Location: children_list.php?page=1');
        exit;
    }
?>

<h1 class="text-center mt-3">Modifier <?=htmlentities($child["firstname"] . " " . $child["lastname"]) ?></h1>

<?php 
    include_once "../../notification.php";
?>

<!-- Un formulaire en PHP ou en JS -->
<!-- Action correspond à l'adresse du fichier ou les données du formulaire iront après validation -->
<form class="w-50 mx-auto mt-4" action="controller/children/update_children_ctrl.php" method="POST">
    <!-- Le prénom de l'enfant -->
    <div class="mb-3">
        <label for="firstname" class="form-label">Prénom de l'enfant</label>
        <input type="text" class="form-control" name="firstname" value="<?= htmlentities($child['firstname']) ?>">
    </div>
    <!-- Le nom de l'enfant -->
    <div class="mb-3">
        <label for="lastname" class="form-label">Nom de l'enfant</label>
        <input type="text" class="form-control" name="lastname" value="<?= htmlentities($child['lastname']) ?>">
    </div>
    <!-- La date de naissance de l'enfant -->
    <div class="mb-3">
        <label for="birthdate" class="form-label">Date de naissance de l'enfant</label>
        <input type="date" class="form-control" name="birthdate" value="<?= $child['birthdate'] ?>">
    </div>
    <!-- La sexe biologique de l'enfant -->
    <div class="form-check">
        <input type="radio" class="form-check-input" name="biosex" value="girl" <?= $child['biosex'] === 'girl' ? 'checked' : '' ?>>
        <label for="biosex" class="form-check-label">Fille</label>
    </div>
    <div class="form-check">
        <input type="radio" class="form-check-input" name="biosex" value="boy" <?= $child['biosex'] === 'boy' ? 'checked' : '' ?>>
        <label for="biosex" class="form-check-label">Garçon</label>
    </div>

    <!-- Provenance -->
    
    <?php
    
    $origins = [
        'groland' => 'Groland',
        'chnord' => 'Le Chnord',
        'gotham' => 'Gotham City',
        'boukistan' => 'Boukistan',
        'neverland' => 'Pays imaginaire'
    ];

    ?>

    <div class="mb-3">
        <label for="origin" class="form-label">Provenence</label>
        <select name="origin" class="form-select">
            <?php foreach ($origins as $value => $label):?>

                <option value="<?= $value ?>" <?= $child['origin'] === $value ? 'selected' : '' ?>><?= $label ?></option>

            <?php endforeach ?>

        </select>
    </div>

    <!-- Description -->
    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea name="description" class="form-control" cols="50" rows="10"><?= htmlentities($child['description']) ?></textarea>               
    </div>

    <input type="hidden" value="<?= $id ?>" name="id_child">

    <div class="text-center">
        <input type="submit" value="Modifier" class="btn btn-primary">
    </div>

</form>