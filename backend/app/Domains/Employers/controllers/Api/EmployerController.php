<?php

namespace App\Domains\Employers\Controllers\Api;

use App\Domains\Employers\Models\EmployerInfo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domains\Employers\Resources\EmployerInfoResource;

class EmployerController extends Controller
{
    public function index(Request $request)
    {
        $employers=EmployerInfo::with(['user','jobs', 'location.country', 'location.city'])
        ->latest()
        ->paginate($request->input('per_page',10));
        return response()->json([
            'success'=>true,
            'data'=>EmployerInfoResource::collection($employers),
            'meta' => [
                'current_page' => $employers->currentPage(),
                'last_page' => $employers->lastPage(),
                'per_page' => $employers->perPage(),
                'total' => $employers->total(),
                'from' => $employers->firstItem(),
                'to' => $employers->lastItem(),
            ],
            'message' => 'employers retrieved successfully.'
        ]);
    }




    public function show(string $id)
    {
        $employer=EmployerInfo::with(['user','jobs', 'location.country', 'location.city'])
        ->find($id);

        if(!$employer){
            return response()->json([
                'success' => false,
                'message' => 'Employer not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new EmployerInfoResource($employer),
            'message' => 'Employer retrieved successfully.'
        ]);
    }



}
