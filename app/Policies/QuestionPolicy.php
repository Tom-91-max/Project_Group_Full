<?php

namespace App\Policies;

use App\Models\Question;
use App\Models\User;

class QuestionPolicy
{
    public function update(User $user, Question $question): bool
    {
        return $user->isAdmin() || $user->id === $question->user_id;
    }

    public function delete(User $user, Question $question): bool
    {
        return $user->isAdmin() || $user->id === $question->user_id;
    }
}


