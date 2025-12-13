<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Services\ProjectVerificationService;

class ProjectVerificationController extends Controller
{
    public function __construct(private ProjectVerificationService $service) {}

    public function start(int $id, Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'channel' => 'required|string|in:email,phone',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        try {
            $pv = $this->service->start($id, $validator->validated()['channel']);
            return response()->json(['success' => true, 'data' => $pv], 200);
        } catch (\Exception $e) {
            $code = match ($e->getMessage()) {
                'project_not_found' => 404,
                'forbidden' => 403,
                'destination_missing' => 400,
                default => 400,
            };
            return response()->json(['success' => false, 'message' => $e->getMessage()], $code);
        }
    }

    public function confirm(int $id, Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'channel' => 'required|string|in:email,phone',
            'code' => 'required|string|min:6|max:6',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        try {
            $pv = $this->service->confirm($id, $validator->validated()['channel'], $validator->validated()['code']);
            return response()->json(['success' => true, 'data' => $pv], 200);
        } catch (\Exception $e) {
            $code = match ($e->getMessage()) {
                'project_not_found' => 404,
                'forbidden' => 403,
                'invalid_code' => 422,
                'expired' => 422,
                'already_used' => 409,
                default => 400,
            };
            return response()->json(['success' => false, 'message' => $e->getMessage()], $code);
        }
    }
}
