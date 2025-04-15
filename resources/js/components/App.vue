<template>
  <form method="post" action="/guess" class="grid justify-center place-content-start min-h-screen gap-16 p-8">
    <h1 class="text-4xl font-bold text-center">TERMO</h1>

    <Row ref="rowComponent" :attempt="attempt" />
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
        csrfToken
      };
    },
    methods: {
      guess() {
        return this.attempt += 1;
      },

      submitForm(inputs) {
        // FORM SUBMIT
        axios.post('/guess', {
          letter_1: inputs[0].value,
          letter_2: inputs[1].value,
          letter_3: inputs[2].value,
          letter_4: inputs[3].value,
          letter_5: inputs[4].value
        }, {
          headers: {
            'X-CSRF-TOKEN': csrfToken,
          }
        })
        .then(res => console.log(res.data))
        .catch(err => console.error(err));
        
        return this.guess();
        // Aqui você pode chamar this.guess() ou submeter o form
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

              this.submitForm(inputs);
            }
            
          }
        }
      });
    },
  };
</script>