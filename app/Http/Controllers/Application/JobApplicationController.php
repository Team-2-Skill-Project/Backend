<?php

namespace App\Http\Controllers\Application;

use App\Http\Controllers\Controller;
use App\Http\Requests\Application\StoreJobApplicationRequest;
use App\Models\Application;
use App\Services\JobApplicationService;
use Illuminate\Http\Request;

class JobApplicationController extends Controller
{
    protected JobApplicationService $applicationService;

    public function __construct(JobApplicationService $applicationService)
    {
        $this->applicationService = $applicationService;
    }

    /**
     * Display a listing of the job applications for the authenticated user, with optional filtering by status and search by job title.
     *
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Application::query()->with(['job.company', 'candidateProfile.user']);


        if ($user->role === 'candidate' || $user->candidateProfile) {
            $query->where('candidate_profile_id', $user->candidateProfile->id);
        }

        // Filter by status if provided in the request
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // additional filter for searching by job title
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('job', function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            });
        }

        // additional filters can be added here as needed
        return response()->json([
            'status' => 'success',
            'data' => $query->latest()->paginate(10)
        ]);
    }

    /**
     * 2. store a new job application and log the initial status in the history table.
     */
    public function store(StoreJobApplicationRequest $request)
    {
        $candidateProfileId = $request->user()->candidateProfile->id;

        $application = $this->applicationService->createApplication($candidateProfileId, $request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'The request has been submitted successfully.',
            'data' => $application->load(['job', 'histories'])
        ], 201);
    }

    /**
     * 3. show the details of a specific job application, including related job and company information, status history, and candidate profile with user information.
     */
    public function show(Application $jobApplication)
    {
        // Load related data for the application, including job details, company, history of status changes, and candidate profile with user information.
        $jobApplication->load(['job.company', 'histories.changer', 'candidateProfile.user']);

        return response()->json([
            'status' => 'success',
            'data' => $jobApplication
        ]);
    }

    /**
     * 4. update the status of a job application and log the change in the history table (Update Status - For Admin or Company Only)
     */
    public function updateStatus(Request $request, Application $Application)
    {
        $request->validate([
            'status' => ['required', 'string'],
            'notes' => ['nullable', 'string', 'max:500']
        ]);

        $updatedApplication = $this->applicationService->updateStatus(
            $Application,
            $request->status,
            $request->notes
        );

        return response()->json([
            'status' => 'success',
            'message' => 'The application status has been successfully updated.',
            'data' => $updatedApplication->load('histories')
        ]);
    }

    /**
     * 5. pull request to withdraw the application (Withdraw Application - For Candidate Only)
     */
    public function withdraw(Application $Application)
    {
        // Check if the authenticated user is the owner of the application
        if ($Application->candidate_profile_id !== auth()->user()->candidateProfile->id) {
            return response()->json(['message' => 'You are not authorized to perform these operations.'], 403);
        }

        $updatedApplication = $this->applicationService->updateStatus(
            $Application,
            'withdrawn',
            'The candidate withdrew the application.'
        );

        return response()->json([
            'status' => 'success',
            'message' => 'The application has been successfully withdrawn.',
            'data' => $updatedApplication
        ]);
    }
}
