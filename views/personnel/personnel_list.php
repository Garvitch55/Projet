<?php

if (!isset($_GET['page']) || $_GET['page'] <= 0) {
    header("Location: ?page=1");
    exit;
}

require_once __DIR__ . "/../../config.php";
requireLogin();
start_page("Liste des personnaires");
$href = "views/staffs/staffs_list.php";

try {
    $pdo = getPDO();

    // Le nombre d'élément par page
    $staffPerPage = 10;

    // Pour récupérer la page où on est, on la mettera dans l'URL
    // En plus si la page dans l'URL n'est pas mise on le forcera à 1
    // (int) est un casting, cela convertira par exemple "14" en 14;
    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

    // if(!isset($_GET['page'])){
    //     header("Location:" . $_SERVER["REQUEST_URI"] . $staffs_list.php?page=1);
    // }

    // On calcul le décalage(offset dans la requête SQL)
    $offset = ($page - 1) * $staffPerPage;
    // par exemple l'offset de la page 14 = 14 - 1 = 13 * 10 = 130

    // On va récupérer le total d'enfant la base de données
    // ce qui nous permettra de connaitre le nombre de page en tout
    // à mettre dans le système de pagination
    $total = $pdo->query("SELECT COUNT(*) FROM staff")->fetchColumn();

    // Imaginons que nous avons 200 enfants, donc cela ferait 20 pages
    // mais il resterait 5 enfants sans page, donc l'arrondissement
    // à l'unité supérieur permet de les afficher, sur une 21e page
    $totalPages = ceil($total / $staffPerPage);

    // On va écrire la requête SQL
    // On demandera en plus que les enfants soient dans l'ordre alphabétique

    $sql = "SELECT id_staff, firstname, lastname, role, phone, city
            FROM staff
            ORDER BY lastname LIMIT :staffPerPage OFFSET :offset";

    // Dans ce cas le binding est inutile (car on récupère tous les enfants) et surtout
    // nous n'utilisons d'input ou de get pour la requete
    // donc l'utilisation de prepare n'est pas obligatoire, on utilise query
    $stmt = $pdo->prepare($sql);

    $stmt->bindValue(':staffPerPage', $staffPerPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    // L'ordre des variables est important elle correspond au point d'interrogation
    $stmt->execute();

    $staffs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // echo "<pre>";
    // var_dump($staffs);
    // echo "</pre>";


} catch (PDOException $e) {
    echo $e->getMessage();
}

?>

<h1 class="text-center mt-3">Liste des personnels</h1>

<table class="table">
    <thead>
        <tr>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Téléphone</th>
            <th>Ville</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
            foreach ($staffs as $st):
                ?>

                <tr>
                    <td><?= htmlentities($st['lastname']) ?></td>
                    <td><?= htmlentities($st['firstname']) ?></td>
                    <td><?= htmlentities($st['phone']) ?></td>
                    <td><?= htmlentities($st['city']) ?></td>
                    <td><?= htmlentities($st['role'] ?? "Pas de rôle définie") ?></td>
                    <td>
                        <a href="views/staffs/update_staffs_form.php?id=<?= $st['id_staff'] ?>"><i class="bi bi-screwdriver"></i></a>
                        <a href="views/staffs/read_staffs.php?id=<?= $st['id_staff'] ?>"><i class="bi bi-search"></i></a>
                        <!-- Ici on injecte des données provenant de php vers le js 
                        grace au data-* d'un élément HTML, de préférence un button ou un input
                        NE JAMAIS MÉLANGER DU PHP AVEC DU JS ! 
                        -->
                        <button class="btn btn-danger btn-sm delete-btn" data-id="<?= $st['id_staff'] ?>" data-csrf="<?= $_SESSION['csrf_token'] ?>"
                        data-name="<?= getFullName($st) ?>" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="bi bi-emoji-dizzy"></i>
                        </button>
                    </td>
                </tr>
        <?php
            endforeach;
?>
    </tbody>
</table>

<!-- Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="deleteModalLabel">Supprimer un membre de personnel</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="delete-staff-message"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        <button type="button" class="btn btn-danger" id="delete-confirm">Supprimer</button>
      </div>
    </div>
  </div>
</div>


<div class="text-center">
    <a class="btn btn-primary" href="views/staffs/add_staffs_form.php">Ajouter un personnel</a>
</div>

    <nav aria-label="Page navigation enfant">
        <ul class="pagination justify-content-center mt-4">
            <!-- max() prend la valeur max(A,B), la valeur la plus grande entre les 2 
                dans l'exemple si dessous, max(1, $page - 1 ); si $page = 1 donc B < A, prendra la valeur A
                on ne pourra jamais être en dessous de 1-->
            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>"><a class="page-link" href="<?= $href ?>?page=<?= max(1, $page - 1) ?>">Précédent</a></li>
            
            <!-- Nous allons afficher les 2 pages précédentes et suivantes 
                imaginons que l'on est à la page 6 on va afficher 5, 6, 7, 8  -->
            <?php

            $start = max(1, $page - 2); //marge des 2 précédents
$end = min($totalPages, $page + 2);
?>

            <!-- Les pages du début et ... -->
            <!-- écriture sans accolade -->
            <?php if ($start > 1): ?>
                <li class="page-item"><a class="page-link" href="<?= $href ?>?page=1">1</a></li>
                <?php if ($start > 2): ?>
                    <li class="page-item"><a class="page-link disabled">...</a></li>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Les 2 pages avant et les 2 pages après -->
            
            <?php for ($i = $start; $i <= $end; $i++): ?>
                <li class="page-item <?= $i === $page ? 'active' : '' ?>"><a class="page-link" href="<?= $href ?>?page=<?= $i ?>"><?= $i ?></a></li>
            <?php endfor; ?>

            <!-- les pages de fin et -->
             <?php if ($end < $totalPages): ?>
                <?php if ($end < $totalPages - 1): ?>
                    <li class="page-item"><a class="page-link disabled">...</a></li>
                <?php endif; ?>
                <li class="page-item"><a class="page-link" href="<?= $href ?>?page=<?= $totalPages ?>"><?= $totalPages ?></a></li>
            <?php endif; ?>
            
            <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>"><a class="page-link" href="<?= $href ?>?page=<?= min($totalPages, $page + 1) ?>">Suivant</a></li>
        </ul>
    </nav>

<script>

    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));

    // Cet variable permet de connaitre en temps réel quel bouton nous avons cliqué 
    // nous lui injecterons plus tard un id.
    let selectId = null;
    let csrfToken = null;

    // On récupère tous les boutons de suppression
    const btns = document.querySelectorAll('.delete-btn');
    const deleteStaffMessage = document.getElementById('delete-staff-message');
    const deleteBtn = document.getElementById('delete-confirm');

    btns.forEach(btn => {
        btn.addEventListener("click", () => {
            selectId = btn.dataset.id;
            csrfToken = btn.dataset.csrfToken;
            const fullName = btn.dataset.name;

            deleteStaffMessage.innerHTML = `Voulez-vous vraiment supprimer <strong>${fullName}</strong> ?`;

            modal.show();
        })
    })

    deleteBtn.addEventListener("click", () => {
        if(selectId !== null) {
            
            // cette objet nous permettra d'envoyer des données en ajax 
            // dans un autre fichier PHP.

            // Permet l'utilisation du $_POST dans le ficher PHP
            let formData = new FormData;
            formData.append('id_staff', selectId);
            formData.append('csrf_token', csrfToken);

            const data = {
                method: "POST",
                //headers: {
                //    "Content-Type" : "application/json" // "clé" : "valeur"
                //}, 
                body: formData

                // sans formData
                // JSON.stringify({
                //     id_staff: selectId
                // })
            }

            fetch("controller/staffs/delete_staffs_ctrl.php", data)
            .then(result => result.json())
            .then(res => {
                console.log(res)
                let status = res.status;
                if(status === "success") {
                    // On va chercher le bouton dont l'ID est cliqué, ensuite on cherche son tr le plus proche
                    // et on le supprime.
                    document.querySelector(`button[data-id='${selectId}']`).closest('tr').remove();
                }
                modal.hide()
                })
            
            

        } else {
            console.error("Pas d'ID selectionné.");
        }
    })
</script>
</body>
</html>