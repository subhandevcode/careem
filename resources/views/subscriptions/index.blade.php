<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}"> <!-- Add CSRF Token -->
    <title>Subscribe to a Plan</title>
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body>
    <h1>Subscribe to a Plan</h1>

    <form id="payment-form" action="{{ route('subscribe.store') }}" method="POST">
        @csrf
        <div id="card-element">
            <!-- A Stripe Element will be inserted here. -->
        </div>
        <button type="submit" id="submit">Subscribe Now</button>
        <div id="error-message"></div>
    </form>

    <script>
        // Initialize Stripe
        const stripe = Stripe("{{ $stripe_key }}"); // Stripe public key
        const elements = stripe.elements();

        const card = elements.create("card");
        card.mount("#card-element");

        const form = document.getElementById("payment-form");

        form.addEventListener("submit", async (event) => {
            event.preventDefault();

            const {token, error} = await stripe.createToken(card);

            if (error) {
                document.getElementById('error-message').innerText = error.message;
            } else {
                // Send the token and CSRF token to your server for subscription creation
                const response = await fetch("{{ route('subscribe.store') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({
                        payment_method_id: token.id,
                        plan: 'monthly', // Use 'monthly' or 'yearly' dynamically
                    }),
                });

                const data = await response.json();
                if (data.success) {
                    alert('Subscription successful!');
                    window.location.href = '/userprofile/show'; // Redirect after success
                } else {
                    alert(data.message); // Show error message
                }
            }
        });
    </script>
</body>
</html>
