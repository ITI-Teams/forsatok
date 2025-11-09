<?php

namespace App\Http\Controllers\Api;

use App\Domains\Candidates\Models\CandidateInfo;
use App\Domains\Location\Models\Locationable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CandidateInfoController extends Controller
{
    public function show(){

        $user=Auth::user();
        if($user->type !== 'candidate'){
            return response()->json(['message'=>'Unauthorized'],403);
        }

        $candidateInfo = CandidateInfo::where('user_id', $user->id)->first();
        if(!$candidateInfo){
            return response()->json(['message'=>'candidate info not found'],404);
        }

        return response()->json($candidateInfo);
    }



    public function update(Request $request){

        $user=Auth::user();
        if($user->type !== 'candidate'){
            return response()->json(['message'=>'Unauthorized'],403);
        }

        $validated = $request->validate([
            'phone' => 'nullable|string',
            'resume' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'education' => 'nullable|string',
            'experience' => 'nullable|string',
            'bio' => 'nullable|string',
            'country_id' => 'nullable|exists:countries,id',
            'city_id' => 'nullable|exists:cities,id',
            'address' => 'nullable|string|max:255',
        ]);

        $candidateInfo = CandidateInfo::firstOrCreate(['user_id' => $user->id]);

        if ($request->hasFile('resume')) {
            $path = $request->file('resume')->store('resumes', 'public');
            $validated['resume'] = $path;
        }

        // Extract location data
        $countryId = $validated['country_id'] ?? null;
        $cityId = $validated['city_id'] ?? null;
        $address = $validated['address'] ?? null;
        unset($validated['country_id'], $validated['city_id'], $validated['address']);

        $candidateInfo->update($validated);

        // Save location
        if ($countryId || $cityId || $address) {
            Locationable::updateOrCreate(
                [
                    'locationable_id' => $candidateInfo->id,
                    'locationable_type' => CandidateInfo::class,
                ],
                [
                    'country_id' => $countryId,
                    'city_id' => $cityId,
                    'address' => $address,
                ]
            );
        } else {
            // Delete location if all fields are empty
            $candidateInfo->location()->delete();
        }

        // Load location relationship
        $candidateInfo->load('location');

        return response()->json([
            'message' => 'Candidate info updated successfully',
            'data' => $candidateInfo,
        ]);

    }
}
