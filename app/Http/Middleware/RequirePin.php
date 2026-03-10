<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class RequirePin
{
    private const MAX_ATTEMPTS = 5;

    private const LOCKOUT_MINUTES = 30;

    public function handle(Request $request, Closure $next): Response
    {
        /** @var User $user */
        $user = $request->user();

        if (! $user->isPinSet()) {
            return response()->json([
                'status' => 'error',
                'message' => 'PIN not configured. Please set up your PIN before proceeding.',
            ], 403);
        }

        if ($user->isPinLocked()) {
            return response()->json([
                'status' => 'error',
                'message' => 'PIN is locked. Try again '.$user->pin_locked_until->diffForHumans().'.',
            ], 429);
        }

        $raw = (string) $request->input('pin', '');

        if ($raw === '' || ! ctype_digit($raw) || strlen($raw) !== 4) {
            return response()->json([
                'status' => 'error',
                'message' => 'A valid 4-digit PIN is required to perform this action.',
            ], 422);
        }

        if (! $user->verifyPin((int) $raw)) {
            $attempts = $user->pin_attempts + 1;
            $update = ['pin_attempts' => $attempts];

            if ($attempts >= self::MAX_ATTEMPTS) {
                $update['pin_locked_until'] = now()->addMinutes(self::LOCKOUT_MINUTES);
                $update['pin_attempts'] = 0;
                $user->update($update);

                return response()->json([
                    'status' => 'error',
                    'message' => 'Too many failed attempts. Your PIN has been locked for 30 minutes.',
                ], 429);
            }

            $user->update($update);

            $remaining = self::MAX_ATTEMPTS - $attempts;

            return response()->json([
                'status' => 'error',
                'message' => "Invalid PIN. {$remaining} ".($remaining === 1 ? 'attempt' : 'attempts').' remaining.',
            ], 401);
        }

        $user->update(['pin_attempts' => 0, 'pin_locked_until' => null]);

        // Remove pin from the request payload so controllers never see it
        $request->request->remove('pin');

        return $next($request);
    }
}
