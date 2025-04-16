<template>
  <div class="grid grid-cols-5 gap-4 w-fit mx-auto z-30">
    <input
      v-for="n in 5"
      :key="n"
      :ref="el => setInputRef(n, el)"
      :name="`row_${row}_letter_${n}`"
      type="text"
      :disabled="attempt !== row"
      v-model="letters[n]"
      :class="inputClass(n)"
      minlength="1"
      maxlength="1"
      autocomplete="off"
      @input="handleInput(n, $event)"
      @focus="handleFocus(n)"
    />
  </div>
</template>

<script setup>
  import { ref, onMounted, reactive } from 'vue';

  // Repassa o evento para <Row.vue />
  const emit = defineEmits(['ready']);

  const inputRefs = ref({});

  // Pegar as props
  const props = defineProps(['attempt', 'row', 'feedback', 'won']);

  // Mantém o [array] para os valores das letras
  const letters = reactive({ 1: '', 2: '', 3: '', 4: '', 5: '' });

  // Adiciona a lógica das classes CSS
  function setInputRef(index, el) {
    if (el) {
      inputRefs.value[index] = el;
    }
  }

  // Pula para o <input /> seguinte quando o valor for preenchido
  function focusNext(index, event) {
    const input = event.target;
    const value = input.value;

    // Move para o próximo <input /> quando o valor for preenchido
    if (value.length === 1 && index < 5) {
      const nextRef = inputRefs.value[index + 1];
      const nextInput = Array.isArray(nextRef) ? nextRef[0] : nextRef;

      if (nextInput && typeof nextInput.focus === 'function') {
        nextInput.focus();
      }
    }
  }

  // Atualiza o [array] de letras
  function handleInput(index, event) {
    const value = event.target.value;
    letters[index] = value;

    // Chama a função para mover o foco
    focusNext(index, event);

    // Emitir 'ready' quando o último <input /> da linha for preenchido
    if (value.length === 1 && index === 5) {
      emit('ready');
    }
  }

  // Foca no <input /> certo quando ele for ativado
  function handleFocus(index) {
    const firstInput = inputRefs.value[index];
    if (firstInput && typeof firstInput.focus === 'function') {
      firstInput.focus();
    }
  }

  onMounted(() => {
    // Foca no primeiro <input /> quando a linha for habilitada
    if (props.attempt === props.row) {
      const firstInput = inputRefs.value[1];
      if (firstInput && typeof firstInput.focus === 'function') {
        firstInput.focus();
      }
    }
  });

  // Adiciona a lógica das classes CSS
  function inputClass(index) {
    const base = 'outline-2 size-18 rounded-lg text-3xl font-bold text-center transition-all select-none';

    // Linha correta = tudo verde, mesmo sem feedback
    if (props.won) {
      return `${base} bg-emerald-500 text-white outline-emerald-600 uppercase select-none`;
    }

    // Linha atual
    if (props.attempt !== props.row) {
      const colorMap = {
        correct: 'bg-emerald-500 text-white outline-emerald-600 uppercase select-none',
        misplaced: 'bg-orange-300 text-white outline-orange-400 uppercase select-none',
        wrong: 'bg-zinc-600 text-white outline-zinc-700 uppercase select-none',
      };

      // Adiciona a lógica das classes CSS
      const feedbackClass = props.feedback?.[index] ? colorMap[props.feedback[index]] : 'bg-white/5 outline-zinc-300/30 uppercase text-white select-none';
      return `${base} ${feedbackClass}`;
    }

    return `${base} bg-white/20 outline-zinc-300 text-white uppercase caret-transparent z-50 border-3 border-transparent focus:border-emerald-300/50 select-none`;
  }
</script>