<?php
// Génération d'une référence de commande unique à chaque chargement de page.
// Format : "CMD" + timestamp
$order_ref = "CMD" . time();
?>

<!DOCTYPE html> 
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyPay — Paiement sécurisé</title>


    <link rel="stylesheet" href="assets/css/style.css">

    <!--
        SDK EasyPay (obligatoire)
        Doit être chargé dans le <head> avant toute utilisation de EcomEasypay
    -->
    <script type="text/javascript" src="assets/js/easypay.sdk.min.js"></script>
</head>

<body>

<div class="container">

    <div class="card">

        <!-- En-tête de la carte de paiement -->
        <div class="card-header">
            <div class="logo-wrap">
                <h1>EasyPay</h1>
            </div>
            <p>Passerelle de paiement sécurisée</p>
        </div>

        <!-- Corps du formulaire -->
        <div class="form-body">

            <!--
                Référence de commande auto-générée (lecture seule).
                Transmise à EasyPay pour identifier la transaction.
            -->
            <div class="input-group">
                <label for="order_ref">Référence commande</label>
                <input type="text"
                       id="order_ref"
                       value="<?= $order_ref ?>"
                       readonly
                       class="readonly-input">
            </div>

            <!-- Nom complet du client -->
            <div class="input-group">
                <label for="customer_name">Nom complet</label>
                <input type="text"
                       id="customer_name">
            </div>

            <!-- Adresse email du client -->
            <div class="input-group">
                <label for="customer_email">Adresse email</label>
                <input type="email"
                       id="customer_email">
            </div>

            <!-- Montant et devise -->
            <div class="row-inputs">

                <!-- Montant à payer (minimum : 1) -->
                <div class="input-group">
                    <label for="amount">Montant</label>
                    <input type="number"
                           id="amount"
                           min="1">
                </div>

                <!--
                    Devise de la transaction.
                    Valeurs acceptées par l'API EasyPay : USD, CDF.
                -->
                <div class="input-group">
                    <label for="currency">Devise</label>
                    <select id="currency">
                        <option value="USD">USD</option>
                        <option value="CDF">CDF</option>
                    </select>
                </div>

            </div>

            <!-- Description de la commande -->
            <div class="input-group">
                <label for="description">Description</label>
                <textarea id="description"></textarea>
            </div>

            <!-- Affichage informatif des canaux de paiement supportés -->
            <div class="channels-info">
                <span class="channels-title">Moyens de paiement acceptés</span>
                <div class="channels-list">
                    <span>Airtel Money</span>
                    <span>Orange Money</span>
                    <span>M-Pesa</span>
                    <span>Visa</span>
                    <span>Mastercard</span>
                </div>
            </div>

            <!--
                Bouton de validation du formulaire.
                Au clic, déclenche la validation des champs et l'initialisation du SDK.
            -->
            <button type="button" id="generateButton">
                Procéder au paiement
            </button>

            <!--
                Conteneur obligatoire pour le SDK EasyPay.
                Le SDK injecte ici le bouton officiel "Payer avec EasyPay"
                après validation des données via easypay.showPayButton().
                L'attribut id="ecom-easypay" est requis par le SDK.
            -->
            <div id="ecom-easypay"></div>

        </div>

    </div>

</div>

<script>

/**
 * Procéder au paiement:
 *   1. Récupère et valide les champs du formulaire
 *   2. Configure les clés API EasyPay
 *   3. Construit l'objet commande (order)
 *   4. Initialise le SDK et affiche le bouton "Payer avec EasyPay"
 */
document.getElementById("generateButton").addEventListener("click", function () {

    // --- Etape 1 : Récupération des valeurs des champs ---
    const customer_name  = document.getElementById("customer_name").value.trim();
    const customer_email = document.getElementById("customer_email").value.trim();
    const amount         = document.getElementById("amount").value.trim();
    const description    = document.getElementById("description").value.trim();
    const currency       = document.getElementById("currency").value; // USD ou CDF

    // --- Validation basique côté client : tous les champs sont requis ---
    if (!customer_name || !customer_email || !amount || !description) {
        alert("Veuillez remplir tous les champs obligatoires.");
        return;
    }

    // --- Validation du montant : doit être strictement positif ---
    if (parseFloat(amount) <= 0) {
        alert("Le montant doit être supérieur à 0.");
        return;
    }

    // --- Etape 2 : Clés d'authentification marchand EasyPay ---
    // Ces clés sont récupérées depuis l'espace développeur
    const api_keys = {
        publishable_key: "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NjI3MzgwNjksImp0aSI6Im9tdUFMcWc1UFNMS3daekRscEFxcWxJbmQ5eVJVRnhacUZRUXFPbWlsbFE9IiwiaXNzIjoiZUNvbVNBUy1TZWN1cmVkU2VydmVyIiwibmJmIjoxNzYyNzM4MDc5LCJleHAiOjE3NjI3NDUyNzksImRhdGEiOnsidXNlcklkIjoxODMxM319.-gSjHQjARH2fEUTrS6Vx4dl4-HAt9ek8Bh4owTb9Hrk",
        correlation_id: "Y05BeXZ4MEk0NThLM3cwNFA3aW9hclhiL2VyQWdkKzB2SklqZjNIYkNGMGhLV0hLSWJiUTZTdz0=",
        mode: "v1" // "sandbox" pour les tests, "v1" pour la production
    };

    // --- Etape 3 : Construction de l'objet commande (order) ---
    
    const order = {
        order_ref:      document.getElementById("order_ref").value, // Référence unique de la commande
        customer_name:  customer_name,   // Nom du client
        customer_email: customer_email,  // Email du client
        description:    description,     // Description de la commande
        currency:       currency,        // Devise choisie par le client
        amount:         String(amount),  // Montant converti en string (exigé par le SDK)
        success_url:    "http://localhost/easypay/success.php", // Redirection si paiement réussi
        error_url:      "http://localhost/easypay/error.php",   // Redirection si paiement échoué
        cancel_url:     "http://localhost/easypay/cancel.php",  // Redirection si paiement annulé
        language:       "fr"             // Langue de l'interface EasyPay (fr ou en)
    };

    // Réinitialise le conteneur au cas où le bouton a déjà été généré précédemment
    document.getElementById("ecom-easypay").innerHTML = "";

    // --- Etape 4 : Initialisation du SDK et affichage du bouton ---
    const easypay = new EcomEasypay(api_keys); // Crée une instance du SDK avec les clés API
    easypay.setOrder(order);                   // Passe les données de commande au SDK
    easypay.showPayButton();                   // Affiche le bouton "Payer avec EasyPay"
});

</script>

</body>
</html>