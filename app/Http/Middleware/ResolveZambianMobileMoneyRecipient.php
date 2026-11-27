<?php

namespace App\Http\Middleware;

use App\Services\LencoMobileMoneyService;
use App\Services\ZambianMobileNumber;
use Closure;
use Illuminate\Http\Request;
use InvalidArgumentException;
use RuntimeException;

class ResolveZambianMobileMoneyRecipient
{
    public function __construct(private LencoMobileMoneyService $lenco)
    {
    }

    public function handle(Request $request, Closure $next)
    {
        try {
            $resolved = $this->lenco->resolve((string) $request->input('phone'));
        } catch (InvalidArgumentException|RuntimeException $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ], 422);
        } catch (\Throwable $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to verify this mobile-money number right now. Please try again.',
            ], 502);
        }

        $request->merge([
            'phone' => $resolved['phone'],
            'recipient_name' => $resolved['account_name'],
            'operator' => $resolved['operator'],
        ]);

        return $next($request);
    }
}
