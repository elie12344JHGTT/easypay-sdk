<?php


// Récupération et sécurisation des paramètres GET transmis par EasyPay
$reference = isset($_GET['reference']) ? htmlspecialchars($_GET['reference']) : null;
$order_ref = isset($_GET['order_ref']) ? htmlspecialchars($_GET['order_ref']) : null;

// Message d'erreur : utilise celui d'EasyPay s'il est fourni, sinon message par défaut
$message   = isset($_GET['message'])   ? htmlspecialchars($_GET['message'])   : 'Une erreur est survenue lors du traitement de votre paiement.';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur de paiement — EasyPay</title>
    <!-- Feuille de style partagée avec index.php -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="result-page">

<div class="container">
    <div class="card result-card error-card">

        <!-- Icône SVG d'erreur (cercle avec croix rouge) -->
        <div class="result-icon error-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <line x1="15" y1="9" x2="9" y2="15"/>
                <line x1="9" y1="9" x2="15" y2="15"/>
            </svg>
        </div>

        <h1 class="result-title">Echec du paiement</h1>

        <!-- Message d'erreur dynamique : fourni par EasyPay ou message générique -->
        <p class="result-subtitle"><?= $message ?></p>

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
                    <!-- Badge visuel indiquant l'échec de la transaction -->
                    <td><span class="badge badge-error">FAILED</span></td>
                </tr>
                <tr>
                    <!-- Date et heure du traitement de la page (côté serveur) -->
                    <td>Date</td>
                    <td><?= date('d/m/Y à H:i:s') ?></td>
                </tr>
            </table>
        </div>
        <?php endif; ?>

        <!-- Lien permettant à l'utilisateur de retenter le paiement -->
        <a href="index.php" class="btn-back">Réessayer le paiement</a>

    </div>
</div>

</body>
</html>