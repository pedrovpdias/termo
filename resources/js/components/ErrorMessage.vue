<template>
  <div id="error-message" v-if="show" class="flex justify-center absolute -top-2 left-0 right-0 mx-auto transition-all animate-error">
    <span class="text-sm text-white bg-red-500 font-semibold px-4 py-1 rounded-full">
      Palavra inválida
    </span>
  </div>
</template>

<script setup>
  const props = defineProps({
    show: Boolean,
  });
</script>