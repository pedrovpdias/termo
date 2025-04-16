<template>
  <div
    v-if="show"
    class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
  >
    <div class="bg-black rounded-2xl text-center w-96 shadow-xl grid gap-2">
      <h2 class="text-2xl font-bold px-4 pt-8">{{ title }}</h2>
      <p class="p-4">{{ message }}</p>

      <div v-if="wordOfTheDay" class="text-sm text-zinc-400 py-2">
        Palavra do dia: <strong class="text-white">{{ wordOfTheDay }}</strong>
      </div>

      <button
        v-if="resultText"
        class="py-1 px-4 hover:bg-zinc-950 focus:bg-zinc-950 outline-none text-sm border border-zinc-900 rounded-full py-2 px-4 mx-auto flex items-center gap-2"
        @click="copyResult"
      >
        <span class="grid place-content-center size-6 rounded-full bg-zinc-900 text-white text-[0.7rem]">
          <i class="bi-share-fill"></i>
        </span>
        Compartilhar resultado
      </button>

      <div v-if="nextGame" class="text-xs text-zinc-500 border-t border-zinc-900 p-4">
        Próximo jogo em: <strong>{{ nextGame }}</strong>
      </div>
    </div>
  </div>
</template>

<script setup>
  const props = defineProps({
    show: Boolean,
    title: String,
    message: String,
    wordOfTheDay: String,
    nextGame: String,
    resultText: String,
  });

  function copyResult() {
    navigator.clipboard.writeText(props.resultText).then(() => {
      alert('Resultado copiado!');
    });
  }
</script>