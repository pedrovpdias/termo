<?php

namespace App\Services;

use App\Models\Words;

class Operations
{
  // Sorteia uma palavra
  public static function drawWord($currentWord = null)
  {
    // Sorteia uma palavra
    $count = Words::all()->count();
    $id = rand(1, $count); 

    // Verifica se já existe uma palavra secreta
    if($currentWord) {
      // Verifica se a palavra secreta atual é igual a palavra sorteada
      do{
        $word = Words::find($id);
      } while($word['word'] == $currentWord);

      // Retorna a palavra sorteada
      return $word['word'];
    }

    // Retorna a palavra sorteada
    else return Words::find($id)['word'];
  }

  // Verifica se a palavra existe na tabela de palavras da base de dados
  public static function checkWord($word)
  {
    $find = Words::where('word', $word)->first();

    return $find ? true : false;
  }
}
