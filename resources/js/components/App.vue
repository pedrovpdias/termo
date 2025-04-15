<template>
  <form method="post" action="/guess" class="grid justify-center place-content-start min-h-screen gap-16 p-8 z-10">
    <h1 class="text-4xl font-bold text-center">TERMO</h1>

    <Row 
      ref="rowComponent"
      :attempt="attempt"
      :feedback="feedbacks[attempt]"
    />
  </form>
</template>

<script>
  import { nextTick } from 'vue';
  import Row from './Row.vue';
  import axios from 'axios';

  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  export default {
    name: 'App',
    components: { Row },
    data() {
      return {
        attempt: 1,
        csrfToken,
        feedbacks: {} // { attempt: { 1: 'correct', 2: 'wrong', ... } }
      };
    },
    methods: {
      guess() {
        return this.attempt++;
      },

      submitForm() {
        // FORM SUBMIT
        const form = document.querySelector('form');
        const inputs = form.querySelectorAll('input');
        const rows = {};

        // Agrupar inputs por linha
        inputs.forEach(input => {
          const [row, , index] = input.name.split('_'); // exemplo: row_1_letter_3
          if (!rows[row]) rows[row] = {};
          rows[row][index] = input.value;
        });

        const guess = {
          attempt: this.attempt,
          rows
        };

        axios.post('/guess', {
          guess
        }, {
          headers: {
            'X-CSRF-TOKEN': csrfToken,
          }
        })
        .then(response => {
          this.feedbacks[this.attempt] = response.data.feedback;
          this.guess(); // próxima linha
        })
        .catch(err => console.error(err));
      },
    },
    mounted() {
      window.addEventListener('keydown', async (event) => {
        if (event.key === 'Enter') {
          await nextTick(); // Make sure refs are available
          const rowComponent = this.$refs.rowComponent;

          if (rowComponent?.rowRefs?.[this.attempt]) {
            const rowDiv = rowComponent.rowRefs[this.attempt];
            const inputs = rowDiv.querySelectorAll('input');
            
            // Verifique se todos estão preenchidos
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