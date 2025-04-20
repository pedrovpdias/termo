<?php

namespace App\Services;

use App\Models\SecretWord;
use App\Models\Words;
use Illuminate\Support\Carbon;

class Operations
{
  protected Carbon $dataBase;

  public function __construct()
  {
    // Sorteia uma data de referência
    $randonYear = mt_rand((date('Y') - 10), date('Y'));
    $randonMonth = mt_rand(1, 12);

    switch ($randonMonth) {
      case 2:
        $randonDay = mt_rand(1, 28);
        break;
      case 4:
      case 6:
      case 9:
      case 11:
        $randonDay = mt_rand(1, 30);
        break;
      default:
        $randonDay = mt_rand(1, 31);
    }

    // Define a data base de referência
    $this->dataBase = Carbon::create($randonYear, $randonMonth, $randonDay);
  }

  // Sorteia uma palavra da base de dados
  public function drawSecretWord(): string
  {
    date_default_timezone_set('America/Sao_Paulo');
    // Busca a palavra do palpite na base de dados
    $getSecretWord = SecretWord::find(1);

    // Verifica se a palavra foi sorteada hoje
    if(date('Y-m-d') == $getSecretWord->updated_at->format('Y-m-d')) {
      $secretWord = $getSecretWord->word;

      // Retorna a palavra do dia
      return $secretWord;
    } 
    
    else {
      // Calcula os dias passados
      $diasPassados = $this->dataBase->diffInDays(Carbon::today());

      // Busca as palavras da base de dados
      $words = Words::where('eligible', true)
          ->orderBy('word')
          ->get();

      if ($words->isEmpty()) {
          return '';
      }

      // Sorteia uma palavra
      $index = $diasPassados % $words->count();
      $secretWord = $words[$index]->word;

      // Salva a palavra na base de dados
      $word = SecretWord::find(1);
      $word->word = $secretWord;
      $word->save();

      // Retorna a palavra do dia
      return $secretWord;
    }
  }

  // Verifica se a palavra existe na tabela de palavras da base de dados
  public static function checkWord($word)
  {
    $find = Words::where('word', $word)->first();

    return $find ? true : false;
  }
}
