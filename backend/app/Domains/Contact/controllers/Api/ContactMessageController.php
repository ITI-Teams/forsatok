<?php

namespace App\Domains\Contact\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domains\Contact\Actions\StoreContactMessageAction;
use App\Domains\Contact\Actions\GetAllContactMessagesAction;
use App\Domains\Contact\Requests\StoreContactMessageRequest;
use App\Domains\Contact\Models\ContactMessage;
use Illuminate\Http\JsonResponse;

class ContactMessageController extends Controller
{
    public function store(StoreContactMessageRequest $request, StoreContactMessageAction $storeAction)
    {
        $message = $storeAction->execute($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully!',
            'data'    => $message
        ], 201);
    }

    public function index(GetAllContactMessagesAction $getAllMessagesAction)
    {
        $messages = $getAllMessagesAction->execute(15);

        return response()->json([
            'success' => true,
            'data' => $messages
        ]);
    }
}
