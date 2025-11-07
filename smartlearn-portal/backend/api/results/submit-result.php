<?php
require_once '../../config/core.php';
require_once '../../models/Result.php';
require_once '../../models/Question.php';
require_once '../../models/Test.php';

class SubmitResult extends Core {
    public function submit() {
        $user_data = $this->validateToken();
        if (!$user_data) {
            $this->sendError("Authentication required");
        }

        $data = json_decode(file_get_contents("php://input"));
        
        if (!isset($data->testId) || !isset($data->answers)) {
            $this->sendError("Test ID and answers are required");
        }

        // Get test and questions
        $test = new Test($this->db);
        $test_data = $test->getById($data->testId);
        
        if (!$test_data) {
            $this->sendError("Test not found");
        }

        $question = new Question($this->db);
        $questions = $question->getByTestId($data->testId);

        // Calculate score
        $score = $this->calculateScore($questions, $data->answers);

        // Save result
        $result = new Result($this->db);
        $result->user_id = $user_data->user_id;
        $result->test_id = $data->testId;
        $result->score = $score;
        $result->time_spent = $data->timeSpent ?? 0;
        $result->answers = $data->answers;

        $result_id = $result->create();
        
        if ($result_id) {
            // Log test completion activity
            $this->logActivity($user_data->user_id, "test_completed", 
                "Completed test: {$test_data['title']} with score: {$score}%");
            
            $this->sendSuccess([
                'message' => 'Result submitted successfully',
                'score' => $score,
                'result_id' => $result_id
            ]);
        } else {
            $this->sendError("Failed to submit result");
        }
    }

    private function calculateScore($questions, $user_answers) {
        $total_questions = count($questions);
        $correct_answers = 0;

        foreach ($questions as $index => $question) {
            $user_answer = $user_answers[$index] ?? null;
            
            if ($user_answer !== null) {
                $question_obj = new Question($this->db);
                $question_obj->type = $question['type'];
                $question_obj->correct_answer = $question['correct_answer'];
                
                if ($question_obj->validateAnswer($user_answer)) {
                    $correct_answers++;
                }
            }
        }

        return $total_questions > 0 ? round(($correct_answers / $total_questions) * 100, 2) : 0;
    }
}

$submitResult = new SubmitResult();
$submitResult->submit();
?>