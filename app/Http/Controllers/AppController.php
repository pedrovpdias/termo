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
        $inputGuess = $request->input('guess');
        $attempt = $inputGuess['attempt'];
        $rows = $inputGuess['rows'];

        $guess = $rows[$attempt];
        $target = strtoupper('MELAO'); // ou uma palavra dinâmica

        $feedback = [];
        $used = array_fill(0, 5, false); // Letras da palavra correta já utilizadas

        // Primeiro: marcar letras corretas
        for ($i = 0; $i < 5; $i++) {
            $letter = strtoupper($guess[$i + 1]);
            $targetLetter = $target[$i];

            if ($letter === $targetLetter) {
                $feedback[$i + 1] = 'correct';
                $used[$i] = true; // marcar essa posição como usada
            }
        }

        // Segundo: marcar letras misplaced
        for ($i = 0; $i < 5; $i++) {
            if (isset($feedback[$i + 1]) && $feedback[$i + 1] === 'correct') {
                continue;
            }

            $letter = strtoupper($guess[$i + 1]);

            // Procurar essa letra em outra posição da palavra
            $found = false;
            for ($j = 0; $j < 5; $j++) {
                if (!$used[$j] && $letter === $target[$j]) {
                    $feedback[$i + 1] = 'misplaced';
                    $used[$j] = true;
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $feedback[$i + 1] = 'wrong';
            }
        }
        
        return response()->json(['feedback' => $feedback]);
    }
}
