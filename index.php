<?php
$order_ref = "CMD" . time();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyPay</title>

    <link rel="stylesheet" href="assets/css/style.css">

    <!-- SDK EasyPay -->
    <script type="text/javascript" src="assets/js/easypay.sdk.min.js"></script>
</head>

<body>

<div class="container">

    <div class="card">

        <h1>EasyPay</h1>
        <p>Formulaire de paiement</p>

        <label>Référence commande</label>
        <input type="text"
               id="order_ref"
               value="<?= $order_ref ?>"
               readonly>

        <label>Nom complet</label>
        <input type="text"
               id="customer_name"
               placeholder="Nom complet">

        <label>Email</label>
        <input type="email"
               id="customer_email"
               placeholder="Email">

        <label>Montant</label>
        <input type="number"
               id="amount"
               placeholder="Montant">

        <label>Description</label>
        <textarea id="description"
                  placeholder="Description"></textarea>

        <button type="button" id="generateButton">
            Générer le paiement
        </button>

        <div id="ecom-easypay"></div>

    </div>

</div>

<script>

document.getElementById("generateButton").addEventListener("click", function () {

    let customer_name = document.getElementById("customer_name").value;
    let customer_email = document.getElementById("customer_email").value;
    let amount = document.getElementById("amount").value;
    let description = document.getElementById("description").value;

    if (
        customer_name === "" ||
        customer_email === "" ||
        amount === "" ||
        description === ""
    ) {
        alert("Veuillez remplir tous les champs.");
        return;
    }

    let api_keys = {
        publishable_key: "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NjI3MzgwNjksImp0aSI6Im9tdUFMcWc1UFNMS3daekRscEFxcWxJbmQ5eVJVRnhacUZRUXFPbWlsbFE9IiwiaXNzIjoiZUNvbVNBUy1TZWN1cmVkU2VydmVyIiwibmJmIjoxNzYyNzM4MDc5LCJleHAiOjE3NjI3NDUyNzksImRhdGEiOnsidXNlcklkIjoxODMxM319.-gSjHQjARH2fEUTrS6Vx4dl4-HAt9ek8Bh4owTb9Hrk",
        correlation_id: "Y05BeXZ4MEk0NThLM3cwNFA3aW9hclhiL2VyQWdkKzB2SklqZjNIYkNGMGhLV0hLSWJiUTZTdz0=",
        mode: "sandbox"
    };

    let order = {
        order_ref: document.getElementById("order_ref").value,
        customer_name: customer_name,
        customer_email: customer_email,
        description: description,
        currency: "USD",
        amount: String(amount),

        success_url: "http://localhost/easypay/success.php",
        error_url: "http://localhost/easypay/error.php",
        cancel_url: "http://localhost/easypay/cancel.php",

        language: "fr"
    };

    document.getElementById("ecom-easypay").innerHTML = "";

    let easypay = new EcomEasypay(api_keys);

    easypay.setOrder(order);

    easypay.showPayButton();

});
</script>

</body>
</html>