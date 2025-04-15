<template>
  <div v-if="attempt == row" class="grid grid-cols-5 gap-4 w-fit mx-auto" >
    <input 
      v-for="n in 5"
      :key="n"
      :ref="el => setInputRef(n, el)"
      :name="`letter_${n}`"
      type="text" 
      class="border-2 backdrop-blur size-18 rounded-lg bg-white/20 border-zinc-300 outline-none transition-all caret-transparent flex text-center text-3xl text-black uppercase font-bold text-white select-none" 
      minlength="1" maxlength="1"
      @input="handleInput(n, $event)"
      autocomplete="off"
    >
  </div>

  <div v-else class="grid grid-cols-5 gap-4 w-fit mx-auto">
    <input 
      v-for="n in 5"
      :key="n"
      :ref="el => setInputRef(n, el)"
      :name="`letter_${n}`"
      type="text" 
      class="border-2 border-zinc-300/30 bg-white/5 backdrop-blur size-18 rounded-lg outline-none transition-all caret-transparent flex text-center text-3xl text-black uppercase font-bold text-transparent select-none" 
      minlength="1" maxlength="1"
      @input="handleInput(n, $event)"
      autocomplete="off"
      disabled
    >
  </div>


</template>

<script setup>
import { ref, } from 'vue';

const emit = defineEmits(['ready']);

const inputRefs = ref({});

const props = defineProps(['attempt', 'row']);

function setInputRef(index, el) {
  if (el) {
    inputRefs.value[index] = el;
  }
}

function focusNext(index, event) {
  const input = event.target;
  const value = input.value;

  if (value.length === 1 && index < 5) {
    const nextRef = inputRefs.value[index + 1];
    const nextInput = Array.isArray(nextRef) ? nextRef[0] : nextRef;

    if (nextInput && typeof nextInput.focus === 'function') {
      nextInput.focus();
    }
  }
}

function handleInput(index, event) {
  focusNext(index, event);

  if (event.target.value.length === 1 && index === 5) {
    emit('ready');
  }
}
</script>