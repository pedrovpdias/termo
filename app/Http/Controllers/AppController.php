<?php

namespace App\Http\Controllers;

use App\Services\Operations;
use Illuminate\Http\Request;

class AppController extends Controller
{
    public function index(Operations $appServices)
    {
        date_default_timezone_set('America/Sao_Paulo');
        // Verifica se já há uma sessão aberta no dia atual
        if(!session('word') || session('session_date') != date('Y-m-d')) {
            // Sorteia uma palavra
            $secretWord = $appServices->drawSecretWord();
            
            // Salva a palavra na sessão
            session(
                [
                    'word' => $secretWord,
                    'session_date' => date('Y-m-d')
                ]
            );
        }   
        
        return view('app');
    }

    // Palpites
    public function guess(Request $request)
    { 
        // Validar o input
        $inputGuess = $request->input('guess');
        $attempt = $inputGuess['attempt'];
        $rows = $inputGuess['rows'];

        $guess = $rows[$attempt];
        $target = strtoupper(session('word')); // Palavra correta

        $feedback = [];
        $used = array_fill(0, 5, false); // Letras da palavra correta já utilizadas

        // Marca as letras corretas
        for ($i = 0; $i < 5; $i++) {
            $letter = strtoupper($guess[$i + 1]);
            $targetLetter = $target[$i];

            if ($letter === $targetLetter) {
                $feedback[$i + 1] = 'correct';
                $used[$i] = true; // marcar essa posição como usada
            }
        }

        // Marca as letras "misplaced"
        for ($i = 0; $i < 5; $i++) {
            if (isset($feedback[$i + 1]) && $feedback[$i + 1] === 'correct') {
                continue;
            }

            $letter = strtoupper($guess[$i + 1]);

            // Procurar essa letra "misplaced" em outra posição da palavra
            $found = false;
            for ($j = 0; $j < 5; $j++) {
                if (!$used[$j] && $letter === $target[$j]) {
                    $feedback[$i + 1] = 'misplaced';
                    $used[$j] = true;
                    $found = true;
                    break;
                }
            }

            // Se a letra não foi encontrada, marcar como "wrong"
            if (!$found) {
                $feedback[$i + 1] = 'wrong';
            }
        }
        
        return response()->json(['feedback' => $feedback, 'correctWord' => session('word')]);
    }

    // Verifica se a palavra existe
    public function checkWord($word)
    {
        // Busca a palavra do palpite na base de dados
        $find = Operations::checkWord($word);

        // Verifica se a palavra existe
        if($find) {
            return response()->json(['exists' => true]);
        }

        else {
            return response()->json(['exists' => false]);
        }
    }
}
