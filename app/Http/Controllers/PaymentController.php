<?php

namespace App\Http\Controllers;

use Log;
use App\Models\User;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    // Show the payment page
    public function showPaymentPage($user_id)
    {
        $user = User::findOrFail($user_id);  // Find the user by user_id
        return view('payment.payment-form', compact('user'));  // Pass user data to the payment view
    }

    // Process the payment
    public function processPayment(Request $request)
    {
        // Validate credit card details (for simplicity, only basic validation)
        $request->validate([
            'card_number' => 'required|numeric|digits:16',
            'expiry_date' => 'required|date_format:m/y',
            'cvv' => 'required|numeric|digits:3',
        ]);

        // Get credit card details
        $cardNumber = $request->input('card_number');
        $expiryDate = $request->input('expiry_date');
        $cvv = $request->input('cvv');

        // Log the request data for debugging
        Log::info('Payment Attempt', [
            'card_number' => $cardNumber,
            'expiry_date' => $expiryDate,
            'cvv' => $cvv
        ]);

        // Simulate a payment gateway request (replace with real bank API)
        $paymentStatus = $this->processBankPayment($cardNumber, $expiryDate, $cvv);

        // Check payment status and redirect accordingly
        if ($paymentStatus === 'success') {
            // Payment successful, redirect to success page
            // return redirect()->route('payment.success'); 
            return redirect()->route('userprofile.edit', ['user_id' => auth()->id()]);

        } else {
            // Payment failed, redirect to failure page
            return redirect()->route('payment.failed'); 
        }
    }

    // Simulate a bank payment response (Replace with actual bank API)
    private function processBankPayment($cardNumber, $expiryDate, $cvv)
    {
        // Simulate a successful payment for a known card number
        if ($cardNumber == '4242424242424242') {
            return 'success'; // For testing purpose
        }

        return 'failed'; // Simulate failed payment
    }
}
