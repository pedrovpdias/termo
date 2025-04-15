<?php

namespace App\Http\Controllers;

use App\Models\SecretWord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AppController extends Controller
{
    public function index()
    { 
        if(!session('word')) {
            $secretWord = new SecretWord();
            if($secretWord['word']) {
                if($secretWord['created_at'] != $secretWord['updated_at']) {
                    $secretWordDate = $secretWord['updated_at'];
                }
                else {
                    $secretWordDate = $secretWord['created_at'];
                }                
            }

            else {
                $secretWordDate = now();
            }

            if(date('Y-m-d') == $secretWordDate->format('Y-m-d')) {
                do {
                    $response = Http::withoutVerifying()->get('https://api.dicionario-aberto.net/random');
                    $data = $response->json();
                } while (strlen($data['word']) != 5);

                session(['word' => $data['word']]);

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
