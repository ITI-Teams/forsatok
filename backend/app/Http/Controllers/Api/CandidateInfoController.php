<?php

namespace App\Http\Controllers\Api;

use App\Domains\Candidates\Models\CandidateInfo;
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
        ]);

        $candidateInfo = CandidateInfo::firstOrCreate(['user_id' => $user->id]);

        if ($request->hasFile('resume')) {
            $path = $request->file('resume')->store('resumes', 'public');
            $validated['resume'] = $path;
        }

        $candidateInfo->update($validated);

        return response()->json([
            'message' => 'Candidate info updated successfully',
            'data' => $candidateInfo,
        ]);

    }
}
