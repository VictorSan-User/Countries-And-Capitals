<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Event\ViewEvent;

class MainController extends Controller
{
    private $app_data;

    public function __construct()
    {
        //carregar app_data.php
        $this->app_data = require(app_path('app_data.php'));
    }

    public function startGame() : View {
        return view('home');
    }

    public function prepareGame(Request $request){
        $request->validate([
            'total_questions' => 'required|integer|min:3|max:30'
        ],
        [
            'total_questions.required' => 'O número de questões é obrigatório',
            'total_questions.integer' => 'O número de questões tem que ser um inteiro',
            'total_questions.min' =>'No mínimo três questões',
            'total_questions.max' => 'No máximo trinta questões'
        ]
        );
        //APOS VALIDARMOS OS DADOS - - - - - - - 
       
        //busquei o total de questões selecionadas pelo usuario
        $total_questions = intval($request->input('total_questions'));

        //preparar a estrutura do quiz
        $quiz = $this->prepareQuiz($total_questions);

        //colocar o quiz na sessão
        session()->put([
            'quiz' => $quiz,
            'total_questions' => $total_questions,
            'current_question' => 1,
            'correct_question' => 0,
            'wrong_answers' => 0
        ]);

        return redirect()->route('game');

    }
    private function prepareQuiz($total_questions){
        $questions = [];
        $total_counstries = count($this->app_data);

        $indexes = range(0, $total_counstries -1);
        //shuffle vai embaralhar as info
        shuffle($indexes);
        //apos embaralhar, vou buscar o numero de questoes no registro
        $indexes = array_slice($indexes, 0, $total_questions);

        //criar o array das perguntas
        $question_number = 1;

        foreach($indexes as $index){

            $question['question_number'] = $question_number++;
            $question['country'] = $this->app_data[$index]['country'];
            $question['correct_answer'] = $this->app_data[$index]['capital'];

            //wrong answers
            $other_capitals = array_column($this->app_data, 'capital');
            //sem as respostas corretas da coluna capital
            $other_capitals = array_diff($other_capitals, [$question['correct_answer']]);
           
            shuffle($other_capitals);
            $question['wrong_answers'] = array_slice($other_capitals, 0, 3);

            //store answer result
            $question['correct'] = null;

            $questions[] = $question;
        }
        return $questions;
    }
    public function game(): View {
        $quiz = session('quiz');
        $total_questions = session('total_questions');
        $current_question = session('current_question');

        //preparar as perguntas pra mostrar na view
        $answers = $quiz[$current_question]['wrong_answers'];
        $answers[] =$quiz[$current_question]['correct_answers'];

        shuffle($answers);

        return view('game')->with([
            'country'=> $quiz[$current_question]['country'],
            'totalQuestions' => $total_questions,
            'currentQuestion' => $current_question,
            'answers' => $answers
        ]);
    }
}
