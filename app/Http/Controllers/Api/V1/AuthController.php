<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
            'token' => $token,
        ]);
    }

    public function phoneLogin(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => ['required', 'string', 'max:50'],
        ]);

        $customer = $this->findCustomerByPhone($request->input('phone'));

        if (! $customer) {
            return response()->json([
                'message' => 'No customer found with this phone number',
            ], 404);
        }

        // Find or create a 'customer'-role user linked to this customer so we
        // can issue Sanctum tokens through the standard auth pipeline.
        $user = User::firstOrCreate(
            ['customer_id' => $customer->id],
            [
                'name' => $customer->name,
                'email' => "customer-{$customer->id}@customer.mbp",
                'password' => Str::random(40),
                'role' => 'customer',
                'phone' => $customer->phone,
                'is_active' => true,
            ],
        );

        $token = $user->createToken('mobile-customer')->plainTextToken;

        $customer->load([
            'villas.villaType',
            'villas.currentStage',
            'villas.status',
            'villas.structuralStatus',
            'villas.finishingStatus',
            'villas.facadeStatus',
            'towerUnits.towerDefinition',
            'towerUnits.currentStage',
            'towerUnits.status',
            'towerUnits.structuralStatus',
            'towerUnits.finishingStatus',
            'towerUnits.facadeStatus',
        ]);

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $user->role,
            ],
            'customer' => new CustomerResource($customer),
            'token' => $token,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }

    /**
     * Look up a customer by phone, tolerating common Iraqi number formats
     * (local 0750..., international +964750..., 00964750..., or just digits).
     */
    private function findCustomerByPhone(string $input): ?Customer
    {
        $digits = preg_replace('/\D/', '', $input);

        $candidates = [$input];

        if ($digits !== '') {
            $candidates[] = $digits;
            $candidates[] = '+'.$digits;

            if (str_starts_with($digits, '964')) {
                $local = substr($digits, 3);
                $candidates[] = $local;
                $candidates[] = '0'.$local;
            } elseif (str_starts_with($digits, '0')) {
                $intl = substr($digits, 1);
                $candidates[] = '964'.$intl;
                $candidates[] = '+964'.$intl;
            } else {
                $candidates[] = '964'.$digits;
                $candidates[] = '+964'.$digits;
            }
        }

        $candidates = array_values(array_unique(array_filter($candidates)));

        return Customer::whereIn('phone', $candidates)->first();
    }
}
