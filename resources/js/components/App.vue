<template>
  <form method="post" action="/guess" class="grid justify-center place-content-start min-h-screen gap-16 p-8 z-10">
    <h1 class="text-4xl font-bold text-center">TERMO</h1>

    <Row 
      ref="rowComponent"
      :attempt="attempt"
      :feedbacks="feedbacks"
      :won-at-row="wonAtRow"
    />
  </form>
</template>

<script>
  import { nextTick } from 'vue';
  import Row from './Row.vue';
  import axios from 'axios';

  // Pegar o token CSRF
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  export default {
    name: 'App',
    components: { Row },
    data() {
      return {
        attempt: 1, // Tentativa
        csrfToken, // Token CSRF
        feedbacks: {}, // { attempt: { 1: 'correct', 2: 'wrong', ... } }
        wonAtRow: null, // Número da linha que ganhou
      };
    },
    methods: {
      guess() {
        return this.attempt++; // Incrementa a tentativa
      },

      submitForm() {
        // Pegar os inputs
        const form = document.querySelector('form');
        const inputs = form.querySelectorAll('input');
        const rows = {};

        // Agrupar <inputs /> por linha
        inputs.forEach(input => {
          const [, row, , index] = input.name.split('_'); // exemplo: row_1_letter_3
          if (!rows[row]) rows[row] = {};
          rows[row][index] = input.value;
        });


        const guess = {
          attempt: this.attempt,
          rows,
        };

        // Enviar a tentativa
        axios.post('/guess', {
          guess
        }, {
          headers: {
            'X-CSRF-TOKEN': csrfToken,
          }
        })
        .then(response => {
          this.feedbacks[this.attempt] = response.data.feedback;
          
          let win = true;

          for (let i = 1; i <= 5; i++) {
            if (this.feedbacks[this.attempt][i] !== 'correct') {
              win = false;
            }
          }

          // Verifica se ganhou
          if (win) {
            this.wonAtRow = this.attempt;
            alert('Você ganhou!');
          }

          // Verifica se perdeu
          if (this.attempt === 6) {
            alert('Você perdeu!');
          }

          // Se não perdeu ou ainda não ganhou, passa para a próxima tentativa
          if(!win && this.attempt !== 6) {
            this.guess();
          }
          
        })
        .catch(err => console.error(err));
      },
    },
    mounted() {
      // Se pressionar Enter, envia a tentativa
      window.addEventListener('keydown', async (event) => {
        if (event.key === 'Enter') {
          await nextTick(); // Esperar para garantir que os <inputs /> estiverem prontos
          const rowComponent = this.$refs.rowComponent;

          // Verifica se o componente <Row.vue /> foi montado
          if (rowComponent?.rowRefs?.[this.attempt]) {
            const rowDiv = rowComponent.rowRefs[this.attempt];
            const inputs = rowDiv.querySelectorAll('input');
            
            // Verifica se todos os <inputs /> estão preenchidos
            const allFilled = [...inputs].every(input => input.value.length === 1);
            if (allFilled) {

              this.submitForm();
            }
            
          }
        }
      });
    },
  };
</script>