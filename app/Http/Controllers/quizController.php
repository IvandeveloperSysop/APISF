<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Http\Store;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\Question;
use App\Models\Promo;
use App\Models\Quiz;
use App\Models\Type;
use App\Models\QuestionAnswer;
use App\Models\UserQuiz;
use App\Models\User;
use App\Models\UserQuestionAnswer;
use App\Models\MiniGame;
use Session;

class quizController extends Controller
{

    protected $date;
    public function __construct()
    {
        $this->date = Carbon::now(new \DateTimeZone('AMERICA/Monterrey'));
    }

    public function index(){

        try {
            $quizzes =  Quiz::with(['promo'])->paginate(10);
            $promos =  Promo::all();

            return view('admin.quizzes.index',['quizzes' => $quizzes, 'promos' => $promos]);
        } catch (\Throwable $th) {
            dd($th);
        }

    }

    public function insert(Request $request){

        try {

            Quiz::create([
                'title' => $request->title_quiz,
                'type_id' => 1,
                'promo_id' => $request->promo_id,
            ]);

            return [
                'result' => 'ok'
            ];
        } catch (\Throwable $th) {

            return [
                'result' => $th
            ];

        }

    }

    public function details($id){

        $quiz = Quiz::find($id);
        $questions = Question::where('quiz_id', $id)
        ->with(['quiz','type'])
        ->get();

        $types = Type::where('table', '=', 'questions')->get();


        // return $questions;

        return view('admin.quizzes.details',['questions' => $questions, 'quiz' => $quiz, 'types' => $types]);


    }

    public function insertQuestion(Request $request){

        $orderMax = DB::table('questions')
        ->where('quiz_id', $request->quiz_id)
        ->orderBy('id','DESC')
        ->first();

        if(isset($orderMax->order)){
            $order = $orderMax->order + 1;
        }else{
            $order = 1;
        }

        // dd($orderMax);

        Question::create([
            'title' => $request->title_question,
            'quiz_id' => $request->quiz_id,
            'type_id' => $request->type_id,
            'order' => $order,
        ]);

        return redirect()->route('quizDetails', ['id' => $request->quiz_id]);
    }

    public function deleteQuestion(Request $request){

        $question = Question::where('id',$request->questionId_delete)->delete();

        return redirect()->route('quizDetails', ['id' => $request->quizId_delete]);
    }

    public function findAnswer(Request $request){

        try {

            $question = Question::find($request->question_id);

            $answers = QuestionAnswer::where('question_id',$request->question_id)
            ->get();

            $bodyT = $this->draTableAnswer($answers);

            return [
                'result' => 'ok',
                'bodyT' => $bodyT,
                'question_id' => $question->id,
                'question' => $question->title,
            ];

        } catch (\Throwable $th) {

            return [
                'result' => $th
            ];

        }

    }

    public function deleteAnswer(Request $request){

        try {
            $answer = QuestionAnswer::where('id',$request->answerId)->first();

            $question = QuestionAnswer::where('id',$request->answerId)->delete();

            $answers = QuestionAnswer::where('question_id',$answer->question_id)
            ->get();

            $bodyT = $this->draTableAnswer($answers);

            return [
                'result' => 'ok',
                'bodyT' => $bodyT,
            ];
        } catch (\Throwable $th) {
            return [
                'result' => $th,
            ];
        }

    }

    public function insertAnswer(Request $request){

        try {
            $correctA = 0;
            if($request->correct_answer == 'true'){
                $correctA = 1;
                QuestionAnswer::where('correct_answer', 1)
                ->where('question_id',$request->question_id)
                ->update(['correct_answer' => 0]);
            }

            QuestionAnswer::create([
                'answer' => $request->answer,
                'correct_answer' => $correctA,
                'question_id' => $request->question_id,
            ]);

            $answers = QuestionAnswer::where('question_id',$request->question_id)
            ->get();

            $bodyT = $this->draTableAnswer($answers);

            return [
                'result' => 'ok',
                'bodyT' => $bodyT,
            ];
        } catch (\Throwable $th) {
            return [
                'result' => $th,
            ];
        }

    }

    public function updateQuestion(Request $request){

        try {

            Question::where('id',$request->question_id)
            ->update([
                'title' => $request->question,
            ]);

            return[
                'result' => 'ok'
            ];

        } catch (\Throwable $th) {
            return[
                'result' => $th
            ];
        }
    }

    public function updateAnswer(Request $request){

        try {
            $answer = QuestionAnswer::where('correct_answer', 1)
            ->where('question_id', $request->question_id)
            ->first();

            $correctA = 1;
            if ($answer) {
                if($request->validAnswer == 'true' && $answer->id != $request->answer_id){
                    QuestionAnswer::where('correct_answer', 1)
                    ->update(['correct_answer' => 0]);

                    $answer = QuestionAnswer::where('id',$request->answer_id)
                    ->update([
                        'correct_answer' => $correctA,
                    ]);
                }
            }else{
                $answer = QuestionAnswer::where('id',$request->answer_id)
                ->update([
                    'correct_answer' => $correctA,
                ]);
            }

            $answer = QuestionAnswer::where('id',$request->answer_id)
            ->update([
                'answer' => $request->answer,
            ]);

            $answers = QuestionAnswer::where('question_id',$request->question_id)
            ->get();

            $bodyT = $this->draTableAnswer($answers);

            return [
                'result' => 'ok',
                'bodyT' => $bodyT,
            ];
        } catch (\Throwable $th) {
            return [
                'result' => $th,
            ];
        }

    }

    public function findQuestionAnswer(Request $request){

        $answer = QuestionAnswer::where('id',$request->answer_id)->first();
        return [
            'answer' => $answer,
            'result' => 'ok'
        ];

    }

    public function draTableAnswer($answers){

        $bodyT = "";
        foreach ($answers as $key => $a) {
            if ($a->correct_answer) {
                $correctAnswer = "<td class='text-success'>Respuesta correcta</td>";
            } else {
                $correctAnswer = "<td class='text-danger'>Respuesta incorrecta</td>";
            }

            $bodyT = $bodyT . "<tr>
                <td>$a->answer</td>
                $correctAnswer
                <td>


                    <div class='row'>
                        <div class='col-md-6'>
                            <a type='button' class='btn btn-success' data-bs-toggle='modal' data-bs-target='#updateAnswerModal' onclick='findAnswer($a->id )'>
                                <i class='fa-solid fa-pencil'></i>Editar
                            </a>
                        </div>
                        <div class='col-md-6'>
                            <a type='button' class='btn btn-danger' onclick='deleteAnswers($a->id )'>
                                <i class='fa-solid fa-trash-can'></i>Borrar
                            </a>
                        </div>
                    </div>
                </td>
            </tr>";
        }

        return $bodyT;

    }

    //  PWA
    public function startQuiz(Request $request){

        try {
            // return [$request->all()];
            $array;
            $questions = Question::with(['quiz','type'])
            ->where('quiz_id',$request->quiz_id)
            ->inRandomOrder()->limit(5)->get();

            $user = User::where('token',$request->user_token)
            ->first();

            $minigameScore = DB::table('minigame_score')
            ->where('id',$request->minigamescore_id)
            ->first();

            $max = Question::where('quiz_id',$request->quiz_id)->orderBy('id','DESC')->first();
            $min = Question::where('quiz_id',$request->quiz_id)->orderBy('id','ASC')->first();

            foreach ($questions as $key => $q) {
                $answers = QuestionAnswer::where('question_id', $q->id)
                ->get();

                $array[] = [
                    'id' => $q->id,
                    'question' => $q->title,
                    'answers' => $answers
                ];
            }

            $validateUserQ = UserQuiz::where('user_id', $user->id)
            ->where('minigame_score_id', $minigameScore->id)
            ->first();

            if(!$validateUserQ){

                UserQuiz::create([
                    'user_id' => $user->id,
                    'quiz_id' => $request->quiz_id,
                    'minigame_score_id' => $minigameScore->id,
                    'total_points' => 0,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ]);

            }

            $userQuiz =  UserQuiz::where('user_id', $user->id)
            ->orderBy('id', 'DESC')
            ->first();

            return [
                'result' => 'ok',
                'array' => $array,
                'miniGame_status' => $minigameScore->status,
                'userQuiz' => $userQuiz,
                'min_id' => $min->id,
                'max_id' => $max->id,
            ];


        } catch (\Throwable $th) {
            return [
                'result' => $th,
            ];
        }

    }

    public function insertUserAnswer(Request $request){

        try {

            $userQAValid = UserQuestionAnswer::where('question_id', $request->question_id)
            ->where('user_quiz_id', $request->userQuiz_id)
            ->first();

            // return [$request->all(), $userQAValid];

            if (!$userQAValid) {
                UserQuestionAnswer::create([
                    'question_id' => $request->question_id,
                    'questions_answer' => $request->answer_id,
                    'user_quiz_id' => $request->userQuiz_id,
                ]);

                $answer = QuestionAnswer::where('id', $request->answer_id)
                ->first();

                if(isset($answer) && $answer->correct_answer == 1){

                    $userQuiz = UserQuiz::where('id', $request->userQuiz_id)
                    ->first();

                    UserQuiz::where('id', $userQuiz->id)
                    ->update([
                        'total_points' => $userQuiz->total_points + 2
                    ]);

                    $correctAnswer_id = $answer->id;
                    $isCorrect = true;
                }else{
                    $answer = QuestionAnswer::where('question_id', $request->question_id)
                    ->where('correct_answer',1)
                    ->first();

                    $correctAnswer_id = $answer->id;
                    $isCorrect = false;
                }

                $userQuiz = UserQuiz::where('id', $request->userQuiz_id)
                ->first();

                return[
                    'result' => 'ok',
                    'answer_select' => $request->answer_id,
                    'correctAnswer_id' => $correctAnswer_id,
                    'question_id' => $request->question_id,
                    'isCorrect' => $isCorrect,
                    'total_points' => $userQuiz->total_points
                ];
            }else{

                $answerV = QuestionAnswer::where('id', $userQAValid->questions_answer)
                ->first();

                if(isset($answerV) && $answerV->correct_answer == 1){
                    $isCorrect = true;
                }else{
                    $isCorrect = false;
                }
                $answer = QuestionAnswer::where('question_id', $request->question_id)
                ->where('correct_answer',1)
                ->first();

                $correctAnswer_id = $answer->id;

                $userQuiz = UserQuiz::where('id', $request->userQuiz_id)
                ->first();

                return [
                    'result' => 'noValid',
                    'correctAnswer_id' => $correctAnswer_id,
                    'answer_select' => $userQAValid->questions_answer,
                    'question_id' => $request->question_id,
                    'isCorrect' => $isCorrect,
                    'total_points' => $userQuiz->total_points,
                    'resp' => $request->all(), $userQAValid,
                ];
            }
        } catch (\Exception $e) {
            //throw $th;
            $json = [
                "message" => $e->getMessage(),
            ];

            return $json;
        }

    }

    public function finishQuiz(Request $request){

        try {

            $userQuiz = UserQuiz::where('id', $request->userQuiz_id)->first();
            $minigame_score = DB::table('minigame_score')
            ->leftJoin('tickets', 'minigame_score.ticket_id','=','tickets.id')
            ->leftJoin('periods_score', 'tickets.period_score_id','=','periods_score.id')
            ->select(
                'minigame_score.id as ms_id',
                'tickets.id as t_id',
                'periods_score.period_id as ps_period_id'
            )
            ->where('minigame_score.id',$userQuiz->minigame_score_id)
            ->first();

            DB::table('extra_points')
            ->insert([
                'user_id' => $userQuiz->user_id,
                'period_id' => $minigame_score->ps_period_id,
                'type_id' => 2,
                'points' => $userQuiz->total_points,
                'status' => 32,
                'ticket_id' => $minigame_score->t_id,
                'refer_or_minigames_id' => $minigame_score->ms_id,
            ]);

            $extra_point = DB::table('extra_points')
            ->where([
                ['user_id', '=', $userQuiz->user_id],
                ['ticket_id', '=', $minigame_score->t_id],
            ])
            ->first();

            // change status of the minigame_score table 23 => pending_valid
            DB::table('minigame_score')
            ->where('id',$userQuiz->minigame_score_id)
            ->update([
                'status' => 23,
                'extra_point_id' => $extra_point->id,
                'points' => $extra_point->points,
            ]);

            return [
                'result' => 'ok'
            ];

        } catch (\Throwable $th) {

            return [
                'result' => $th
            ];

        }



    }

}
