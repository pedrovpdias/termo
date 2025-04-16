<template>
  <section class="grid gap-4 z-20">
    <div v-for="row in 6" :key="row" :ref="el => setRowRef(row, el)" :class="{'animate-shake': props.shakeRow === row}">
      <Letter 
        :attempt="attempt" 
        :row="row"
        :feedback="props.feedbacks[row]"
        :won="props.wonAtRow === row"
        @ready="onReady"
      />
    </div>
  </section>
</template>

<script setup>
  import { ref } from 'vue';
  import Letter from './Letter.vue';

  // Pegar as props
  const props = defineProps(['attempt', 'feedbacks', 'wonAtRow', 'shakeRow']); // feedbacks: { 1: { 1: 'correct', 2: 'wrong', ... }, 2: { 1: 'correct', 2: 'wrong', ... }, ... }
  const rowRefs = ref({}); // { 1: <div>, 2: <div>, ... }

  // Repassa o evento para <App.vue />
  const emit = defineEmits(['ready']);

  // Adiciona a lógica das classes CSS
  function setRowRef(row, el) {
    if (el) {
      rowRefs.value[row] = el;
    }
  }

  // Repassa o evento para <App.vue />
  function onReady() {
    emit('ready'); // repassa o evento para App.vue
  }

  // Expoe para o componente pai (<App.vue />)
  defineExpose({
    rowRefs
  });
</script>