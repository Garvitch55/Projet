<?php 

if(!isset($_GET['page']) || $_GET['page'] <= 0){
    header("Location: ?page=1");
    exit;
}

require_once __DIR__ . "/../../config.php";
requireLogin();
start_page("Liste des personnaires");
$href = "views/children/children_list.php";

try {
    
    $pdo = getPDO();

    // Le nombre d'élément par page 
    $childPerPage = 10;

    // Pour récupérer la page où on est, on la mettera dans l'URL
    // En plus si la page dans l'URL n'est pas mise on le forcera à 1
    // (int) est un casting, cela convertira par exemple "14" en 14;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

    // if(!isset($_GET['page'])){
    //     header("Location:" . $_SERVER["REQUEST_URI"] . $children_list.php?page=1);
    // }

    // On calcul le décalage(offset dans la requête SQL)
    $offset = ($page - 1) * $childPerPage;
    // par exemple l'offset de la page 14 = 14 - 1 = 13 * 10 = 130 

    // On va récupérer le total d'enfant la base de données
    // ce qui nous permettra de connaitre le nombre de page en tout
    // à mettre dans le système de pagination
    $total = $pdo->query("SELECT COUNT(*) FROM child")->fetchColumn();

    // Imaginons que nous avons 200 enfants, donc cela ferait 20 pages 
    // mais il resterait 5 enfants sans page, donc l'arrondissement 
    // à l'unité supérieur permet de les afficher, sur une 21e page
    $totalPages = ceil($total / $childPerPage);

    // On va écrire la requête SQL
    // On demandera en plus que les enfants soient dans l'ordre alphabétique
    // Ici on gère maintenant aussi le soft delete
    $sql = "SELECT id_child, firstname, lastname, birthdate, biosex, origin
            FROM child
            WHERE is_delete = '0'
            ORDER BY lastname LIMIT :childPerPage OFFSET :offset";

    // Dans ce cas le binding est inutile (car on récupère tous les enfants) et surtout
    // nous n'utilisons d'input ou de get pour la requete
    // donc l'utilisation de prepare n'est pas obligatoire, on utilise query
    $stmt = $pdo->prepare($sql);

    $stmt->bindValue(':childPerPage', $childPerPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    // L'ordre des variables est important elle correspond au point d'interrogation
    $stmt->execute();

    $children = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // echo "<pre>";
    // var_dump($children);
    // echo "</pre>";


} catch (PDOException $e) {
    echo $e->getMessage();
}

?>

<h1 class="text-center mt-3">Liste des persionnaires</h1>

<?php 
    include_once "../../notification.php";
?>

<table class="table">
    <thead>
        <tr>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Date de naissance</th>
            <th>Sexe biologique</th>
            <th>Origine</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
            foreach($children as $child) {
                $dob = new DateTime($child['birthdate']); 
                $formatter = new IntlDateFormatter(
                    'fr_FR',
                    IntlDateFormatter::FULL,
                    IntlDateFormatter::NONE,
                    'Europe/Paris'
                );

                $date = $formatter->format($dob); 
                ?>

                <tr>
                    <td><?= htmlentities($child['lastname']) ?></td>
                    <td><?= htmlentities($child['firstname']) ?></td>
                    <td><?= ucfirst($date) ?></td>
                    <td><?= htmlentities($child['biosex'] === 'boy' ? 'Garçon' : 'Fille') ?></td>
                    <td><?= htmlentities(ucfirst($child['origin'])) ?></td>
                    <td>
                        <a href="views/children/update_children_form.php?id=<?= $child['id_child'] ?>"><i class="bi bi-screwdriver"></i></a>
                        <a href="views/children/read_children.php?id=<?= $child['id_child'] ?>"><i class="bi bi-search"></i></a>
                        <button class="btn btn-danger btn-sm delete-btn" data-id="<?= $child['id_child'] ?>"
                        data-name="<?= getFullName($child) ?>" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="bi bi-emoji-dizzy"></i>
                        </button>
                    </td>
                </tr>
        <?php
            } 
        ?>
    </tbody>
</table>

<!-- Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="deleteModalLabel">Supprimer un pensionnaire</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="delete-staff-message"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        <!-- Formulare de suppression -->
        <form action="/../orphelinat/controller/children/delete_children_ctrl.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="hidden" name="id_child" id="inputIdChild">
            <input type="submit" class="btn btn-danger" value="Supprimer">
        </form>
      </div>
    </div>
  </div>
</div>

<div class="text-center">
    <a class="btn btn-primary" href="fixtures/children_creator.php">Générer 200 enfants</a>
    <a class="btn btn-primary" href="views/children/add_children_form.php">Ajouter un pensionnaire</a>
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
    const modal = document.getElementById('deleteModal');
    const modalMessage = document.getElementById('delete-staff-message');
    const inputIdChild = document.getElementById('inputIdChild');
    
    modal.addEventListener("show.bs.modal", function(e) {
        console.log(e);
        // Pour récupérer les données, on récupère le relativeTarget qui contient le bouton qui a activé le modal,
        // cela évite de faire une boucle sur chaque élément
        let btn = e.relatedTarget;
        let childId = btn.dataset.id;
        let childName = btn.dataset.name;

        inputIdChild.value = childId;
        modalMessage.innerHTML = `Voulez-vous supprimer le pensionnaire : <strong>${childName}</strong> ?`;
    })
    
</script>

</body>
</html>