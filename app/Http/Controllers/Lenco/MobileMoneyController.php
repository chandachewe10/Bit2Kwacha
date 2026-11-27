<?php

namespace App\Http\Controllers\Lenco;

use App\Http\Controllers\Controller;
use App\Services\LencoMobileMoneyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use RuntimeException;

class MobileMoneyController extends Controller
{
    public function resolve(Request $request, LencoMobileMoneyService $lenco)
    {
        $validated = $request->validate([
            'phone' => ['required', 'string', 'max:20'],
            'operator' => ['nullable', 'string', 'in:mtn,airtel,zamtel'],
        ]);

        try {
            return response()->json([
                'status' => 'success',
                'data' => $lenco->resolve($validated['phone'], $validated['operator'] ?? null),
            ]);
        } catch (InvalidArgumentException $exception) {
            return response()->json(['status' => 'error', 'message' => $exception->getMessage()], 422);
        } catch (RuntimeException $exception) {
            Log::warning('Lenco mobile-money number resolution failed', ['message' => $exception->getMessage()]);
            return response()->json(['status' => 'error', 'message' => $exception->getMessage()], 422);
        } catch (\Throwable $exception) {
            Log::error('Lenco mobile-money resolution error', ['message' => $exception->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Unable to verify this mobile-money number right now. Please try again.'], 502);
        }
    }
}
