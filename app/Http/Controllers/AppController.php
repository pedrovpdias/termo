<?php

namespace App\Http\Controllers;

use App\Models\SecretWord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AppController extends Controller
{
    public function index()
    {   
        date_default_timezone_set('America/Sao_Paulo');
        if(!session('word')) { 
            $secretWord = SecretWord::find(1);
            
            if($secretWord) {
                if($secretWord['created_at'] != $secretWord['updated_at']) {
                    $secretWordDate = $secretWord['updated_at'];
                }
                else {
                    $secretWordDate = $secretWord['created_at'];
                }

                if(date('Y-m-d') == $secretWordDate->format('Y-m-d')) {
                    $checkSecretWordDate = true;
                }
                else {
                    $checkSecretWordDate = false;
                }
            }

            else {
                $checkSecretWordDate = false;
            }

            if(!$secretWord || !$checkSecretWordDate) {
                do {
                    $response = Http::withoutVerifying()->get('https://api.dicionario-aberto.net/random');
                    $data = $response->json();
                } while (strlen($data['word']) != 5);

                session(['word' => $data['word']]);

                if(!$secretWord) {
                    $secretWord = new SecretWord;
                }
                $secretWord->word = $data['word'];
                $secretWord->save();
            }
            
            else {
                session(['word' => $secretWord['word']]);
            }
        }  
        //echo session('word');
        return view('app');
    }

    public function guess(Request $request)
    {
        $letter_1 = $request->input('letter_1');
        $letter_2 = $request->input('letter_2');
        $letter_3 = $request->input('letter_3');
        $letter_4 = $request->input('letter_4');
        $letter_5 = $request->input('letter_5');       

        $guess = $letter_1.$letter_2.$letter_3.$letter_4.$letter_5;
        $guess = strtolower($guess);
        echo json_encode(['guess' => $guess]);
        
        $word = session('word');

        if($guess == $word) {
            echo json_encode(['result' => 'win']);
        }
        else {
            echo json_encode(['result' => 'lose']);
        }

        exit();

    }
}
