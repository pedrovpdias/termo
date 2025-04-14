<template>
  <form method="post" action="/guess"  class="grid justify-center place-content-start min-h-screen gap-16 p-8">
    <h1 class="text-4xl font-bold text-center">
      TERMO
    </h1>

    <section class="grid gap-4">
      <div v-for="i in 6" :key="i" :ref="`row_${i}`" class="grid grid-cols-5 gap-4 w-fit mx-auto">
        <input 
          v-for="n in 5"
          :key="n"
          :ref="`input_${n}`" :id="`input_${n}`"
          :name="`letter_${n}`"
          type="text" 
          class="border-2 border-zinc-300/30 bg-white/5 backdrop-blur size-18 rounded-lg hover:scale-105 focus:scale-105 hover:bg-white/10 focus:bg-white/30 hover:border-zinc-500 focus:border-zinc-300 outline-none transition-all caret-transparent flex text-center text-3xl text-black uppercase font-bold text-white select-none" 
          maxlength="1" minlength="1"
          @input="focusNext(n, $event)"
          autocomplete="off"
        >
      </div>

    </section>
  </form>
</template>

<script>
  export default {
    data() {
      return {
        message: '',
      };
    },

    methods: {
      focusNext(index, event) {
        const input = event.target;
        const value = input.value;

        if (value.length === 1 && index < 5) {
          const nextRef = this.$refs[`input_${(index + 1)}`];

          // Se for array, pegue o primeiro elemento
          const nextInput = Array.isArray(nextRef) ? nextRef[0] : nextRef;

          if (nextInput && typeof nextInput.focus === 'function') {
            nextInput.focus();
          }
        }
      }
    },
  };
</script>