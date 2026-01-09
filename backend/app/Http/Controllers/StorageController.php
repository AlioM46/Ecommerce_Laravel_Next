<?php

namespace App\Http\Controllers;

use App\Services\StorageService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

class StorageController extends Controller
{
    private  $storageService;

    public function __construct(StorageService $storageService)
    {
        $this->storageService = $storageService;
    }

    /**
     * Upload a file to R2
     */
    public function upload(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'file' => 'required|file|max:10240', // 10MB max
            ]);

            $file = $request->file('file');


            $result = $this->storageService->upload($file);

            return response()->json([
                'isSuccess' => true,
                'message' => 'File uploaded successfully',
                'data' => $result,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'isSuccess' => false,
                'message' => 'Upload failed: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get temporary signed URL (private access)
     */
    public function temporaryUrl(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'key' => 'required|string',
                'minutes' => 'nullable|integer|min:1|max:10080',
            ]);

            $key = $request->input('key');
            $minutes = $request->input('minutes', 120);

            $url = $this->storageService->temporaryUrl($key, $minutes);

            return response()->json([
                'isSuccess' => true,
                'url' => $url,
                'expires_in_minutes' => $minutes,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'isSuccess' => false,
                'message' => 'Failed to generate temporary URL: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get permanent public URL
     */
    public function permanentUrl(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'key' => 'required|string',
            ]);

            $key = $request->input('key');
            $url = $this->storageService->permanentUrl($key);

            return response()->json([
                'isSuccess' => true,
                'url' => $url,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'isSuccess' => false,
                'message' => 'Failed to get URL: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Check if file exists
     */
    public function exists(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'key' => 'required|string',
            ]);

            $key = $request->input('key');
            $exists = $this->storageService->exists($key);

            return response()->json([
                'isSuccess' => true,
                'exists' => $exists,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'isSuccess' => false,
                'message' => 'Failed to check file: ' . $e->getMessage(),
            ], 400);
        }
    }
}

