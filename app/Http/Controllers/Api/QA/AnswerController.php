<?php

namespace App\Http\Controllers\Api\QA;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\StoreAnswerRequest;
use App\Http\Requests\UpdateAnswerRequest;
use App\Models\Answer;
use App\Models\Question;
use Illuminate\Support\Facades\Storage;

class AnswerController extends BaseApiController
{
    /**
     * Store a new answer for a question
     * POST /api/questions/{question}/answers
     */
    public function store(StoreAnswerRequest $request, Question $question)
    {
        $data = $request->validated();
        
        // Set user_id and question_id
        $data['user_id'] = auth()->id();
        $data['question_id'] = $question->id;

        // Handle image upload if exists
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('answers', 'public');
            $data['image_path'] = $path;
        }

        $answer = Answer::create($data);

        // Update question status to 'answered' if it was 'open'
        if ($question->status === 'open') {
            $question->update(['status' => 'answered']);
        }

        // Load relationships for response
        $answer->load('user');

        return $this->success($answer, 'Answer submitted successfully.', 201);
    }

    /**
     * Update the specified answer
     * PUT /api/answers/{answer}
     */
    public function update(UpdateAnswerRequest $request, Answer $answer)
    {
        // Authorization: only owner or admin
        $this->authorize('update', $answer);

        $data = $request->validated();

        // Handle new image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($answer->image_path) {
                Storage::disk('public')->delete($answer->image_path);
            }
            
            $path = $request->file('image')->store('answers', 'public');
            $data['image_path'] = $path;
        }

        $answer->update($data);
        $answer->load('user');

        return $this->success($answer, 'Answer updated successfully.');
    }

    /**
     * Remove the specified answer
     * DELETE /api/answers/{answer}
     */
    public function destroy(Answer $answer)
    {
        // Authorization: only owner or admin
        $this->authorize('delete', $answer);

        // Delete image if exists
        if ($answer->image_path) {
            Storage::disk('public')->delete($answer->image_path);
        }

        $answer->delete();

        return $this->success(null, 'Answer deleted successfully.');
    }

    /**
     * Accept an answer as the best answer
     * POST /api/answers/{answer}/accept
     */
    public function accept(Answer $answer)
    {
        $question = $answer->question;

        // Authorization: only question owner or admin can accept
        if (auth()->id() !== $question->user_id && !auth()->user()->isAdmin()) {
            return $this->error('You cannot accept answer for this question.', 403);
        }

        // Remove previous best answer if exists
        Answer::where('question_id', $question->id)
            ->where('is_accepted', true)
            ->update(['is_accepted' => false]);

        // Set this answer as accepted
        $answer->update(['is_accepted' => true]);

        // Update question status to 'resolved'
        $question->update(['status' => 'resolved']);

        $answer->load('user');

        return $this->success($answer, 'Answer marked as best answer.');
    }
}

// 7. CONTROLLER - AnswerController
// Path: app/Http/Controllers/Api/QA/AnswerController.php
// ============================================================================