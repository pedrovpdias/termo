<template>
  <div
    v-if="show"
    class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
  >
    <div class="bg-black rounded-2xl p-6 text-center w-96 shadow-xl border border-black">
      <h2 class="text-xl font-bold mb-4">{{ title }}</h2>
      <p class="mb-2">{{ message }}</p>

      <div v-if="wordOfTheDay" class="text-sm text-gray-600 mt-4">
        Palavra do dia: <strong class="text-white">{{ wordOfTheDay }}</strong>
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
});
</script>