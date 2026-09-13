<?php
namespace App\Http\Controllers\cv;

use App\Http\Controllers\Controller;
use App\Http\Requests\cv\UploadCvRequest;
use App\Models\CvDocument;
use App\Models\CvExtraction;
use App\Services\CvService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CvController extends Controller
{
    use AuthorizesRequests;
    public function __construct(protected CvService $cvService) {}

    public function store(UploadCvRequest $request): JsonResponse
    {
        $profile = $request->user()->candidateProfile;

        if (!$profile) {
            return response()->json(['message' => 'Candidate profile not found.'], 404);
        }

        $cvDocument = $this->cvService->uploadOrReplaceCv($profile, $request->file('cv'));

        return response()->json([
            'message' => 'CV uploaded successfully and queued for processing.',
            'data'    => $cvDocument,
        ], 201);
    }


    public function show(Request $request, CvDocument $cvDocument): JsonResponse
    {
        $this->authorize('view', $cvDocument);

        $cvDocument->load('extractions');

        return response()->json([
            'data' => $cvDocument,
        ]);
    }


    public function history(Request $request): JsonResponse
    {
        $profile = $request->user()->candidateProfile;

        $history = CvDocument::where('candidate_profile_id', $profile->id)
            ->orderBy('version', 'desc')
            ->get();

        return response()->json(['data' => $history]);
    }

    public function retry(Request $request, CvDocument $cvDocument): JsonResponse
    {
        $this->authorize('update', $cvDocument);

        $updatedCv = $this->cvService->retryProcessing($cvDocument);

        return response()->json([
            'message' => 'CV processing retried successfully.',
            'data'    => $updatedCv,
        ]);
    }

    public function verify(Request $request, CvExtraction $extraction): JsonResponse
    {
        $profile = $request->user()->candidateProfile;

        $this->cvService->verifyAndSyncExtractedData($profile, $extraction, $request->validated());

        return response()->json([
                'message' => 'Extracted data verified and synced to profile successfully.',
            ]);
        }
    }
