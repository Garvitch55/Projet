<?php
if (isset($_GET['status']) && isset($_GET['message'])) {
    $status = $_GET['status'];
    $message = $_GET['message'];

    $icon = '';
    if ($status === 'danger') {
        $icon = '<i class="bi bi-x-circle-fill text-danger" me-2></i>';
    } elseif ($status === 'success') {
        $icon = '<i class="bi bi-check-circle-fill text-success me-2"></i>';
    }
    ?>
    <p class="notification notification-<?= htmlentities($status) ?>">
        <?= $icon ?>
        <?= htmlentities($message) ?>
    </p>
<?php
}
?>