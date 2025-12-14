<?php

namespace App\Http\Controllers\Api\QA;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\StoreQuestionRequest;
use App\Http\Requests\UpdateQuestionRequest;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QuestionController extends BaseApiController
{
    /**
     * Display a listing of questions
     * GET /api/questions
     * Filters: ?status=open&q=yellow+leaves
     */
    public function index(Request $request)
    {
        $query = Question::with(['user', 'tank'])
            ->withCount('answers');

        // Filter by status
        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        // Search by title or content
        if ($search = $request->query('q')) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Order by latest
        $questions = $query->orderByDesc('created_at')->paginate(20);

        return $this->success($questions);
    }

    /**
     * Store a newly created question
     * POST /api/questions
     */
    public function store(StoreQuestionRequest $request)
    {
        $data = $request->validated();
        
        // Set user_id from auth (NEVER trust client)
        $data['user_id'] = auth()->id();
        
        // Set default status
        $data['status'] = 'open';

        // Handle image upload if exists
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('questions', 'public');
            $data['image_path'] = $path;
        }

        $question = Question::create($data);

        // Load relationships for response
        $question->load(['user', 'tank']);

        return $this->success($question, 'Question created successfully.', 201);
    }

    /**
     * Display the specified question with answers
     * GET /api/questions/{question}
     */
    public function show(Question $question)
    {
        // Load relationships
        $question->load([
            'user',
            'tank',
            'answers' => function($query) {
                $query->with('user')
                      ->orderByDesc('is_accepted')  // Best answer first
                      ->orderByDesc('created_at');
            }
        ]);

        return $this->success($question);
    }

    /**
     * Update the specified question
     * PUT /api/questions/{question}
     */
    public function update(UpdateQuestionRequest $request, Question $question)
    {
        // Authorization: only owner or admin
        $this->authorize('update', $question);

        $data = $request->validated();

        // Handle new image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($question->image_path) {
                Storage::disk('public')->delete($question->image_path);
            }
            
            $path = $request->file('image')->store('questions', 'public');
            $data['image_path'] = $path;
        }

        $question->update($data);
        $question->load(['user', 'tank']);

        return $this->success($question, 'Question updated successfully.');
    }

    /**
     * Remove the specified question
     * DELETE /api/questions/{question}
     */
    public function destroy(Question $question)
    {
        // Authorization: only owner or admin
        $this->authorize('delete', $question);

        // Delete image if exists
        if ($question->image_path) {
            Storage::disk('public')->delete($question->image_path);
        }

        $question->delete();

        return $this->success(null, 'Question deleted successfully.');
    }
}




// 6. CONTROLLER - QuestionController
// Path: app/Http/Controllers/Api/QA/QuestionController.php
// ============================================================================