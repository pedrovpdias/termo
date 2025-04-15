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
    { dd($request->all());
        $guess = $request->input('rows')[$request->input('attempt')];
        $target = strtoupper('MELAO'); // ou uma palavra dinâmica

        $feedback = [];

        for ($i = 1; $i <= 5; $i++) {
            $letter = strtoupper($guess[$i]);
            $targetLetter = $target[$i - 1];

            if ($letter === $targetLetter) {
                $feedback[$i] = 'correct'; // verde
            } elseif (str_contains($target, $letter)) {
                $feedback[$i] = 'misplaced'; // amarelo
            } else {
                $feedback[$i] = 'wrong'; // cinza
            }
        }

        return response()->json([
            'feedback' => $feedback
        ]);
    }
}
