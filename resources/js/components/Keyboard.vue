<template>
  <div class="grid gap-2 text-center z-20">
    <div v-for="row in layout" :key="row" class="flex justify-center gap-1">
      <button
        v-for="key in row"
        :key="key"
        class="px-2 py-1 bg-zinc-700 text-white rounded"
        @click="$emit('key', key)"
      >
        {{ key }}
      </button>
    </div>
  </div>
</template>

<script setup>
const layout = [
  ['Q', 'W', 'E', 'R', 'T', 'Y', 'U', 'I', 'O', 'P'],
  ['A', 'S', 'D', 'F', 'G', 'H', 'J', 'K', 'L'],
  ['⌫', 'Z', 'X', 'C', 'V', 'B', 'N', 'M', 'Enter']
];
</script>