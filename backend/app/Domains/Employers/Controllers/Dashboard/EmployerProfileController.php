<?php

namespace App\Domains\Employers\Controllers\Dashboard;

use App\Domains\Employers\Actions\GetCurrentEmployerInfoAction;
use App\Domains\Employers\Actions\UpdateEmployerInfoAction;
use App\Domains\Employers\Models\EmployerInfo;
use App\Domains\Employers\Requests\UpdateEmployerInfoRequest;
use App\Domains\Location\Models\Locationable;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

/**
 * Employer Profile Controller
 *
 * Handles employer profile operations in the dashboard.
 */
class EmployerProfileController extends Controller
{
    /**
     * Get the current employer's profile.
     */
    public function show(Request $request, GetCurrentEmployerInfoAction $getInfo): JsonResponse
    {
        $info = $getInfo->execute($request->user()->id);

        return response()->json([
            'status' => true,
            'data' => $info ?
                $info->load('location.city', 'location.country')
                : null,
        ]);
    }

    /**
     * Update the employer's profile.
     */
    public function update(Request $request, UpdateEmployerInfoAction $updateAction): JsonResponse
    {
        $user = $request->user();
        $form = new UpdateEmployerInfoRequest();

        $rules = array_merge(
            $form->rules(),
            [
                'email' => ['required', 'email', 'max:255'],
                'country_id' => ['nullable', 'exists:countries,id'],
                'city_id' => ['nullable', 'exists:cities,id'],
                'address' => ['nullable', 'string', 'max:255'],
                'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:2048'],
            ]
        );

        $validated = Validator::make(
            $request->all(),
            $rules,
            $form->messages()
        )->validate();

        $info = EmployerInfo::firstOrNew(['user_id' => $user->id]);
        $info = $updateAction->execute($info, $validated);
        $info->refresh();

        if ($user->email !== $validated['email']) {
            $user->email = $validated['email'];
            $user->email_verified_at = null;
            $user->save();
        }

        if ($request->file('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $avatarPath = $request->file('avatar')->store('avatars/employers', 'public');
            $user->avatar = $avatarPath;
            $user->save();
        }

        $countryId = $validated['country_id'] ?? null;
        $cityId = $validated['city_id'] ?? null;
        $address = isset($validated['address']) ? trim($validated['address']) : null;

        if ($countryId || $cityId || $address) {
            Locationable::updateOrCreate(
                [
                    'locationable_id' => $info->id,
                    'locationable_type' => EmployerInfo::class,
                ],
                [
                    'country_id' => $countryId,
                    'city_id' => $cityId,
                    'address' => $address,
                ]
            );
        } elseif ($info->location) {
            $info->location()->delete();
        }

        return response()->json([
            'status' => true,
            'data' => $info->load('location.country', 'location.city'),
        ]);
    }
}
