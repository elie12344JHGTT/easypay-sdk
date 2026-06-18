<?php


// ---- 1. Sécurité : accepter uniquement les requêtes POST ----
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Méthode non autorisée');
}

// ---- 2. Lire le corps de la requête JSON ----
$raw_body = file_get_contents('php://input');
$data     = json_decode($raw_body, true);

if (!$data) {
    http_response_code(400);
    exit('Données invalides');
}

// ---- 3. Extraire les informations ----
$order_ref         = $data['transaction']['order_ref']  ?? 'N/A';
$transaction_ref   = $data['transaction']['reference']  ?? 'N/A';
$payment_channel   = $data['payment']['channel']        ?? 'N/A';
$payment_status    = $data['payment']['status']         ?? 'N/A';
$payment_reference = $data['payment']['reference']      ?? 'N/A';

// ---- 4. Logger la notification dans un fichier ----
$log_dir  = __DIR__ . '/logs';
$log_file = $log_dir . '/ipn.log';

if (!is_dir($log_dir)) {
    mkdir($log_dir, 0755, true);
}

$log_entry = sprintf(
    "[%s] order_ref=%s | status=%s | channel=%s | ref=%s\n",
    date('Y-m-d H:i:s'),
    $order_ref,
    $payment_status,
    $payment_channel,
    $payment_reference
);

file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);

// ---- 5. Traitement métier selon le statut ----
switch ($payment_status) {

    case 'SUCCESS':
        // TODO : Marquer la commande comme payée dans votre base de données
        // Exemple : update_order_status($order_ref, 'paid');
        break;

    case 'DECLINED':
        // TODO : Marquer la commande comme échouée
        // Exemple : update_order_status($order_ref, 'failed');
        break;

    case 'CANCELED':
        // TODO : Marquer la commande comme annulée
        // Exemple : update_order_status($order_ref, 'cancelled');
        break;

    default:
        // Statut inconnu — loggé mais aucune action
        break;
}

// ---- 6. Répondre 200 OK à EasyPay (obligatoire) ----
http_response_code(200);
echo json_encode(['status' => 'received']);
