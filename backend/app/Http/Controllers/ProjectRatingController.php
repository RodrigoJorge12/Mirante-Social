<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Services\ProjectRatingService;

class ProjectRatingController extends Controller
{
    public function __construct(private ProjectRatingService $service) {}

    public function upsert(int $id, Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:0|max:5',
            'feedback_text' => 'nullable|string|max:2000',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        try {
            $v = $validator->validated();
            $saved = $this->service->upsert($id, (int)$v['rating'], $v['feedback_text'] ?? null);
            return response()->json(['success' => true, 'data' => $saved], 200);
        } catch (\Exception $e) {
            $code = match ($e->getMessage()) {
                'not_authenticated' => 401,
                'invalid_rating' => 422,
                'project_not_found' => 404,
                default => 400,
            };
            return response()->json(['success' => false, 'message' => $e->getMessage()], $code);
        }
    }

    public function summary(int $id): JsonResponse
    {
        $s = $this->service->getSummary($id);
        return response()->json(['success' => true, 'data' => $s], 200);
    }
    public function mine(int $id): JsonResponse
    {
        try {
            $r = $this->service->getMine($id);
            return response()->json(['success' => true, 'data' => $r], 200);
        } catch (\Exception $e) {
            $code = $e->getMessage() === 'not_authenticated' ? 401 : 400;
            return response()->json(['success' => false, 'message' => $e->getMessage()], $code);
        }
    }
    public function list(int $id, Request $request): JsonResponse
    {
        $page = (int) ($request->query('page', 1));
        $size = (int) ($request->query('size', 10));
        $items = $this->service->list($id, max(1, $page), max(1, min(50, $size)));
        return response()->json(['success' => true, 'data' => $items], 200);
    }
}
