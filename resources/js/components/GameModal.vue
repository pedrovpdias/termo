<template>
  <div
    v-if="show"
    class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
  >
    <div class="bg-black rounded-2xl text-center w-96 shadow-xl grid gap-4">
      <h2 class="text-2xl font-bold p-4 pt-8">{{ title }}</h2>
      <p class="p-4">{{ message }}</p>

      <div v-if="wordOfTheDay" class="text-sm text-zinc-400">
        Palavra do dia: <strong class="text-white">{{ wordOfTheDay }}</strong>
      </div>

      <div v-if="nextGame" class="text-xs text-zinc-500 border-t border-zinc-900 p-4">
        Próximo jogo em: <strong>{{ nextGame }}</strong>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  show: Boolean,
  title: String,
  message: String,
  wordOfTheDay: String,
  nextGame: String,
});
</script>