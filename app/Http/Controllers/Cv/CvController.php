<?php

namespace App\Http\Controllers\Cv;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cv\UploadCvRequest;
use App\Http\Requests\Cv\VerifyCvExtractionRequest;
use App\Http\Resources\CvDocumentResource;
use App\Models\CvDocument;
use App\Models\CvExtraction;
use App\Services\CvService;
use App\Services\Ai\CvExtractionService; // استدعاء خدمة الذكاء الاصطناعي
use App\Traits\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class CvController extends Controller
{
    use ApiResponse, AuthorizesRequests;

    public function __construct(
        protected CvService $cvService,
        protected CvExtractionService $cvExtractionService // حقن خدمة الـ AI هنا
    ) {}

    /**
     * Upload a new CV document, link it to candidate profile, and start AI extraction.
     */
    public function store(UploadCvRequest $request)
    {
        $user = $request->user();

        // 1. التأكد من وجود البروفايل أو إنشاؤه لمنع إيرور الـ Database
        $profile = $user->candidateProfile()->firstOrCreate(
            ['user_id' => $user->id],
            ['full_name' => $user->name ?? 'Candidate']
        );

        // 2. رفع الملف وإدارة الإصدارات عبر الخدمة الأساسية
        $cvDocument = $this->cvService->uploadOrReplaceCv($profile, $request->file('cv'));

        // 3. تشغيل الذكاء الاصطناعي لتحليل البيانات واستخراجها فوراً
        try {
            $this->cvExtractionService->extractAndPersist($cvDocument);
        } catch (\Exception $e) {
            // لو الـ AI حصل فيه مشكلة، الـ CV ارفع وتخزن عادي، بس بنعلم الحالة إن فيها خطأ
            // عشان السيستم ما يقعش كله (Fail Gracefully)
            $cvDocument->update(['parsing_status' => 'failed']);
        }

        $cvDocument->load('extractions');

        return (new CvDocumentResource($cvDocument))
            ->additional([
                'status' => 'success',
                'message' => __('cv.uploaded_success') ?: 'تم رفع وتحليل السيرة الذاتية بالذكاء الاصطناعي بنجاح.',
            ])
            ->response()
            ->setStatusCode(201);
    }
    /**
     * Display the specified CV document along with its extractions.
     */
    public function show(Request $request, CvDocument $cvDocument)
    {
        $this->authorize('view', $cvDocument);
        $cvDocument->load('extractions');

        return new CvDocumentResource($cvDocument);
    }

    /**
     * Display a listing of the CV documents for the authenticated user, ordered by version.
     */
    public function history(Request $request)
    {
        $profile = $request->user()->candidateProfile;

        if (!$profile) {
            return $this->errorResponse('cv.profile_not_found', 404);
        }

        $history = CvDocument::where('candidate_profile_id', $profile->id)
            ->orderBy('version', 'desc')
            ->get();

        return CvDocumentResource::collection($history);
    }

    /**
     * Retry processing a CV document that previously failed.
     */
    public function retry(Request $request, CvDocument $cvDocument)
    {
        $this->authorize('update', $cvDocument);

        $updatedCv = $this->cvService->retryProcessing($cvDocument);

        // إعادة محاولة الاستخراج بالذكاء الاصطناعي
        try {
            $this->cvExtractionService->extractAndPersist($updatedCv);
        } catch (\Exception $e) {
            $updatedCv->update(['parsing_status' => 'failed']);
        }

        $updatedCv->load('extractions');

        return (new CvDocumentResource($updatedCv))
            ->additional([
                'status' => 'success',
                'message' => __('cv.retried_success') ?: 'تم إعادة محاولة التحليل بنجاح.',
            ]);
    }

    /**
     * Verify the extracted data from a CV extraction and sync it to the candidate profile.
     */
    public function verify(VerifyCvExtractionRequest $request, CvExtraction $extraction)
    {
        $profile = $request->user()->candidateProfile;

        if (!$profile) {
            return $this->errorResponse('cv.profile_not_found', 404);
        }

        $this->cvService->verifyAndSyncExtractedData($profile, $extraction, $request->validated());

        return $this->successResponse(null, 'cv.verified_success');
    }
}
