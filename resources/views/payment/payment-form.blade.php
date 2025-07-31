<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/payment.css">
    <title>Payment Form</title>
</head>
<body>
    <div class="payment-wrapper">
        <div class="payment-container">
            <h1>Secure Payment</h1>
            <form id="payment-form" action="{{ route('payment.process') }}" method="POST">
                @csrf
                <div class="input-group">
                    <label for="card_number">Card Number</label>
                    <input type="text" name="card_number" id="card_number" placeholder="1234 5678 9101 1121" required>
                </div>

                <div class="input-group">
                    <label for="expiry_date">Expiry Date (MM/YY)</label>
                    <input type="text" name="expiry_date" id="expiry_date" placeholder="MM/YY" required>
                </div>

                <div class="input-group">
                    <label for="cvv">CVV</label>
                    <input type="text" name="cvv" id="cvv" placeholder="123" required>
                </div>

                <button type="submit" id="submit">Pay Now</button>
            </form>
        </div>
    </div>
</body>
</html>
