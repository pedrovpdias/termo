<template>
  <section class="grid gap-4 z-20">
    <div v-for="row in 6" :key="row" :ref="el => setRowRef(row, el)">
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

  const props = defineProps(['attempt', 'feedbacks', 'wonAtRow']);
  const rowRefs = ref({});

  const emit = defineEmits(['ready']);

  function setRowRef(row, el) {
    if (el) {
      rowRefs.value[row] = el;
    }
  }

  function onReady() {
    emit('ready'); // repassa o evento para App.vue
  }

  // 🔥 This exposes rowRefs to the parent (App.vue)
  defineExpose({
    rowRefs
  });
</script>