<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Services\ReportService;

class ReportController extends Controller
{
    public function __construct(private ReportService $service) {}

    public function create(Request $request): JsonResponse
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'social_project_id' => 'required|integer',
            'category' => 'required|string|min:3|max:50',
            'reason' => 'required|string|min:10|max:1000',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        try {
            $report = $this->service->create($validator->validated());
            return response()->json(['success' => true, 'data' => $report], 201);
        } catch (\Exception $e) {
            $code = 400;
            if ($e->getMessage() === 'not_authenticated') $code = 401;
            if ($e->getMessage() === 'already_reported') $code = 409;
            if ($e->getMessage() === 'project_not_found') $code = 404;
            return response()->json(['success' => false, 'message' => $e->getMessage()], $code);
        }
    }

    public function mine(): JsonResponse
    {
        try {
            $list = $this->service->getMyReports();
            return response()->json(['success' => true, 'data' => $list], 200);
        } catch (\Exception $e) {
            $code = $e->getMessage() === 'not_authenticated' ? 401 : 500;
            return response()->json(['success' => false, 'message' => $e->getMessage()], $code);
        }
    }

    public function pending(): JsonResponse
    {
        $list = $this->service->listPending();
        return response()->json(['success' => true, 'data' => $list], 200);
    }

    public function resolve(int $id, Request $request): JsonResponse
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'resolution' => 'required|string|in:resolved,rejected',
            'resolution_notes' => 'nullable|string|max:2000',
            'project_action' => 'nullable|string|in:none,suspend,remove',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        $data = $validator->validated();
        $action = $data['project_action'] ?? 'none';
        try {
            $updated = $this->service->resolve($id, $data['resolution'], $data['resolution_notes'] ?? null, $action === 'none' ? null : $action);
            return response()->json(['success' => true, 'data' => $updated], 200);
        } catch (\Exception $e) {
            $code = 400;
            if ($e->getMessage() === 'not_authenticated') $code = 401;
            if ($e->getMessage() === 'report_not_found') $code = 404;
            return response()->json(['success' => false, 'message' => $e->getMessage()], $code);
        }
    }
}
