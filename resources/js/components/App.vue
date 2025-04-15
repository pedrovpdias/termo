<template>
  <form method="post" action="/guess" class="grid justify-center place-content-start min-h-screen gap-16 p-8">
    <input type="hidden" name="_token" :value="csrfToken">

    <h1 class="text-4xl font-bold text-center">TERMO</h1>
    <Row ref="rowComponent" :attempt="attempt" />
  </form>
</template>

<script>
  import { nextTick } from 'vue';
  import Row from './Row.vue';

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

      submitForm() {
        // FORM SUBMIT
        const form = document.querySelector('form');
        form.submit();
        
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
              console.log('Tudo preenchido, pode submeter!');
              this.submitForm();
            }
            
          }
        }
      });
    },
  };
</script>