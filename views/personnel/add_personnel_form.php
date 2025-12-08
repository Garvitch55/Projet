<?php 
    require_once __DIR__ . "/../../config.php";
    start_page("Ajouter un nouveau membre du personnel");
?>

<h1 class="text-center mt-3">Ajouter un nouveau membre du personnel</h1>

<?php 
    include_once "../../notification.php";
?>

<div class="container">
    <div class="card bg-light w-50 mx-auto">
        <div class="card-body p-4 p-md-5">
            <form action="controller/staffs/add_staffs_ctrl.php" method="POST">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="firstname" class="form-label">Prénom <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="firstname" required>
                    </div>
                    <div class="col-md-6">
                        <label for="lastname" class="form-label">Nom <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="lastname" required>
                    </div>
                </div>
                <div class="my-3">
                    <label for="mail" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="mail" required>
                </div>
                <div class="my-3">
                    <label for="psw" class="form-label">Mot de passe <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" name="psw" required>
                </div>
                <div class="my-3">
                    <label for="psw_confirmation" class="form-label">Confirmation du mot de passe <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" name="psw_confirmation" required>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="birthdate" class="form-label">Date de naissance</label>
                        <input type="date" class="form-control" name="birthdate" required>
                    </div>
                    <div class="col-md-6">
                        <label for="phone" class="form-label">Téléphone</label>
                        <input type="texy" class="form-control" name="phone">
                    </div>
                </div>
                <div class="my-3">
                    <label for="city" class="form-label">Ville</label>
                    <input type="text" class="form-control" name="city">
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary w-50 py-2 mt-3">Ajouter le membre</button>
                </div>
            </form>    
        </div>
    </div>
</div>


</body>
</html>