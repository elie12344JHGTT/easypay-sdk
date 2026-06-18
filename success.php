<?php


// Récupération et sécurisation des paramètres GET transmis par EasyPay
$reference = isset($_GET['reference']) ? htmlspecialchars($_GET['reference']) : null;
$order_ref = isset($_GET['order_ref']) ? htmlspecialchars($_GET['order_ref']) : null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement réussi — EasyPay</title>
    <!-- Feuille de style partagée avec index.php -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="result-page">

<div class="container">
    <div class="card result-card success-card">

        <!-- Icône SVG de succès (cercle avec coche verte) -->
        <div class="result-icon success-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="9 12 11 14 15 10"/>
            </svg>
        </div>

        <h1 class="result-title">Paiement réussi !</h1>
        <p class="result-subtitle">Votre transaction a été traitée avec succès.</p>

        <!--
            Tableau des détails de transaction.
            Affiché uniquement si EasyPay a transmis au moins un paramètre dans l'URL.
        -->
        <?php if ($reference || $order_ref): ?>
        <div class="transaction-details">
            <h3>Détails de la transaction</h3>
            <table>
                <?php if ($order_ref): ?>
                <tr>
                    <td>Référence commande</td>
                    <td><strong><?= $order_ref ?></strong></td>
                </tr>
                <?php endif; ?>
                <?php if ($reference): ?>
                <tr>
                    <td>Référence EasyPay</td>
                    <td><strong><?= $reference ?></strong></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td>Statut</td>
                    <!-- Badge visuel indiquant le statut de la transaction -->
                    <td><span class="badge badge-success">SUCCESS</span></td>
                </tr>
                <tr>
                    <!-- Date et heure du traitement de la page (côté serveur) -->
                    <td>Date</td>
                    <td><?= date('d/m/Y à H:i:s') ?></td>
                </tr>
            </table>
        </div>
        <?php endif; ?>

        <!-- Lien de retour vers le formulaire de paiement -->
        <a href="index.php" class="btn-back">Effectuer un nouveau paiement</a>

    </div>
</div>

</body>
</html>