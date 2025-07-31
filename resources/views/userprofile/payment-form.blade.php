<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Form</title>
</head>
<body>

    <h1>Pay with Credit/Debit Card</h1>

    <form id="payment-form" action="{{ route('payment.process') }}" method="POST">
        @csrf
        <label for="card_number">Card Number</label>
        <input type="text" name="card_number" id="card_number" placeholder="Enter your card number" required>

        <label for="expiry_date">Expiry Date (MM/YY)</label>
        <input type="text" name="expiry_date" id="expiry_date" placeholder="MM/YY" required>

        <label for="cvv">CVV</label>
        <input type="text" name="cvv" id="cvv" placeholder="CVV" required>

        <button type="submit" id="submit">Pay Now</button>
    </form>

</body>
</html>
