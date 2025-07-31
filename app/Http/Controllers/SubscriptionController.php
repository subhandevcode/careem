<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Subscription;
use Stripe\PaymentMethod;
use App\Models\Subscription as UserSubscription;

class SubscriptionController extends Controller
{
    // Display subscription page with Stripe public key
    public function showSubscriptionPage(Request $request)
    {
        return view('subscriptions.index', ['stripe_key' => env('STRIPE_KEY')]);
    }

    // Handle the subscription creation
    public function createSubscription(Request $request)
    {
        // Retrieve the Stripe secret key from the environment
        Stripe::setApiKey(env('STRIPE_SECRET'));

        // Validate the incoming request
        $request->validate([
            'payment_method_id' => 'required|string',
            'plan' => 'required|string',
        ]);

        $user = auth()->user();

        try {
            // Create the Stripe customer
            $customer = Customer::create([
                'email' => $user->email,
                'payment_method' => $request->payment_method_id,
                'invoice_settings' => [
                    'default_payment_method' => $request->payment_method_id,
                ],
            ]);

            // Create the subscription
            $subscription = Subscription::create([
                'customer' => $customer->id,
                'items' => [
                    ['price' => $request->plan], // Your price ID from Stripe
                ],
                'expand' => ['latest_invoice.payment_intent'],
            ]);

            // Save subscription details in the database
            $user->subscription()->create([
                'stripe_id' => $subscription->id,
                'stripe_status' => $subscription->status,
                'plan' => $request->plan,
                'ends_at' => now()->addMonth(), // Set expiry based on your plan
            ]);

            return response()->json([
                'subscription' => $subscription,
                'success' => true,
                'message' => 'Subscription successful!',
            ]);

        } catch (\Exception $e) {
            \Log::error('Stripe subscription creation failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Subscription creation failed. Please try again.',
            ]);
        }
    }
}