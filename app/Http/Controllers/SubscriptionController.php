<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscription;


class SubscriptionController extends Controller
{
    //
    public function state(Request $request)
    {
        $userId = $request->get('userIdFk');
        $subscriptionState = $request->input('subscriptionState');

        // Validar que el valor de userIdFk no sea nulo
        if (empty($userId)) {
            return response()->json(['error' => 'El valor de userIdFk es nulo.'], 400);
        }

        // Actualizar la suscripción existente o crear una nueva
        $subscription = Subscription::where('userIdFk', $userId)->first();
        if ($subscription) {
            $subscription->subscriptionState = $subscriptionState;
            $subscription->save();
        } else {
            Subscription::insert([
                'userIdFk' => $userId,
                'subscriptionState' => $subscriptionState,
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function getSubscription(Request $request)
    {
        $userId = $request->get('userIdFK');

        $subscription = Subscription::select(
            'subscriptions.userIdFK',
            'subscriptions.subscriptionState'
        )->where('subscriptions.userIdFK', '=', $userId)->get();

        if ($subscription->isEmpty()){
            return response()->json(['message' => 'Nohay subscripcion'], 500);
        } else {
            return response()->json($subscription, 200);
        }
    }

}
