<?php 
    require_once __DIR__ . "/../../config.php";
    start_page("Inscription");
?>

<h1 class="text-center mt-3">Inscription :</h1>
<p class="text-center mt-3">Votre compte client vous servira d'avoir accès à la demande de devis en ligne.</p>

<?php 
    include_once "../../notification.php";
?>

<!-- Un formulaire en PHP ou en JS -->
<!-- Action correspond à l'adresse du fichier ou les données du formulaire iront après validation -->
<form class="w-50 mx-auto mb-3" style="margin-top: 150px;" action="controller/client/add_client_ctrl.php" method="POST">
    
<div class="row">
<!-- prénom -->
    <div class="mb-3 w-50">
        <label for="firstname" class="form-label required">Votre prénom : <span class="text-danger">*</span></label>
        <input type="text" class="form-control" name="firstname" required>
    </div>
    <!-- nom -->
    <div class="mb-3 w-50">
        <label for="lastname" class="form-label required" require>Votre nom : <span class="text-danger">*</span></label>
        <input type="text" class="form-control" name="lastname" required>
    </div>
</div>
        <!-- numéro de téléphone -->
    <div class="mb-3">
    <label for="phone" class="form-label required">
        Votre numéro de téléphone : <span class="text-danger">*</span>
    </label>
    <input 
        type="tel" 
        name="phone" 
        id="phone"
        class="form-control" 
        placeholder="Entrez votre numéro de téléphone" 
        required
        pattern="[0-9]{10}" 
        title="Veuillez entrer un numéro à 10 chiffres"
    >
</div>


        <!-- adresse mail -->
         <div class="row">
<div class="mb-3 w-50">
    <label for="email" class="form-label required">
        Votre adresse e-mail : <span class="text-danger">*</span>
    </label>
    <input 
        type="email" 
        name="email" 
        id="email"
        class="form-control" 
        placeholder="Entrez votre adresse e-mail" 
        required
    >
</div>
<div class="mb-3 w-50">
    <label for="email_confirm" class="form-label required">
        Confirmer votre adresse e-mail : <span class="text-danger">*</span>
    </label>
    <input 
        type="email" 
        name="email_confirm" 
        id="email_confirm"
        class="form-control" 
        placeholder="Confirmez votre adresse e-mail" 
        required
    >
</div>

</div>


    <!-- votre date naissance -->
    <div class="mb-3">
        <label for="birthdate" class="form-label">Votre date de naissance : </label>
        <input type="date" class="form-control" name="birthdate">
    </div>



<!-- Rue -->
<div class="mb-3">
    <label for="street" class="form-label required">
        Rue : <span class="text-danger">*</span>
    </label>
    <input 
        type="text" 
        class="form-control" 
        name="street" 
        id="street" 
        placeholder="Numéro et nom de la rue" 
        required
    >
</div>

<!-- Ville -->
<div class="mb-3">
    <label for="city" class="form-label required">
        Ville : <span class="text-danger">*</span>
    </label>
    <input 
        type="text" 
        class="form-control" 
        name="city" 
        id="city" 
        placeholder="Entrez votre ville" 
        required
    >
</div>

<!-- Code postal -->
<div class="mb-3">
    <label for="postal_code" class="form-label required">
        Code postal : <span class="text-danger">*</span>
    </label>
    <input 
        type="text" 
        class="form-control" 
        name="postal_code" 
        id="postal_code" 
        placeholder="Entrez votre code postal" 
        required
    >
</div>




    <!-- Votre demande -->
    <div class="mb-3">
        <label for="demande" class="form-label required">Votre demande : <span class="text-danger">*</span></label>
        <textarea name="demande" class="form-control" style="height: 200px;" placeholder="Ecrivez nous vos besoins on vous contactera dans les plus brefs délais." cols="50" rows="10"  required></textarea>
    </div>

    <div class="text-center">
        <input type="submit" value="Vous inscrire" class="btn btn-primary">
    </div>

</form>