<template>
  <main class="grid justify-center place-content-start min-h-screen p-8 z-10">
    <form method="post" action="/guess" class="grid justify-center place-content-start gap-8 p-8 z-10">
      <img :src="logo" alt="Termo" class="w-38 h-auto mx-auto" />

      <Row 
        ref="rowComponent"
        :attempt="attempt"
        :feedbacks="feedbacks"
        :won-at-row="wonAtRow"
        :shakeRow="shakeRow"
      />
    </form>

    <Keyboard @key="handleVirtualKey" />

    <GameModal
      :show="showModal"
      :title="modalTitle"
      :message="modalMessage"
      :word-of-the-day="correctWord"
      :next-game="nextGameCountdown"
    />
  </main>
</template>



<script>
  import { nextTick } from 'vue';
  
  import Row from './Row.vue';
  import Keyboard from './Keyboard.vue';
  import GameModal from './GameModal.vue';
  
  import logo from '../../images/logo.svg';
  import confetti from 'canvas-confetti';
  
  import axios from 'axios';

  // Pegar o token CSRF
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  export default {
    name: 'App',
    components: { Row, Keyboard, GameModal },
    data() {
      return {
        logo,
        attempt: 1, // Tentativa
        csrfToken, // Token CSRF
        feedbacks: {}, // { attempt: { 1: 'correct', 2: 'wrong', ... } }
        wonAtRow: null, // Número da linha que ganhou
        shakeRow: null, // Número da linha que deve se movimentar (caso a palavra seja inexistente)
        showModal: false, // Exibe o modal
        modalTitle: '', // Título do modal
        modalMessage: '', // Mensagem do modal
        correctWord: '', // Palavra correta    
        nextGameCountdown: '', // Contagem regressiva para o próximo jogo
      };
    },
    methods: {
      guess() {
        this.attempt++; // Incrementa a tentativa
        
        // Foca no primeiro <input /> da linha
        if (this.attempt > 6) return;

        else {
          this.focusFirstInput();

          return;
        }
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

        // Monta a palavra enviada
        const currentWord = rows[this.attempt][1]+rows[this.attempt][2]+rows[this.attempt][3]+rows[this.attempt][4]+rows[this.attempt][5];
        
        // Verifica se a palavra atual existe
        axios.post(`check-word/${currentWord}`, 
        {},
        {
          headers: {
            'X-CSRF-TOKEN': csrfToken,
          }
        })
          .then(response => response.data)
          .then(data => data.exists)
          .then(exists => {
            if(exists){
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

                  // Exibe e configura o modal em caso de vitória
                  this.modalTitle = 'Vitória!';
                  this.modalMessage = 'Parabéns, você acertou a palavra do dia!';
                  this.correctWord = currentWord;
                  this.showModal = true;

                  // Exibe animação de "confetti"
                  this.showConfetti();
                }

                // Verifica se perdeu
                if (!win && this.attempt === 6) {
                  // Exibe e configura o modal em caso de derrota
                  this.modalTitle = 'Fim de jogo!';
                  this.modalMessage = 'Você usou todas as tentativas.';
                  this.correctWord = response.data.correctWord; // talvez você precise recuperar isso do backend
                  this.showModal = true;
                }

                // Se não perdeu ou ainda não ganhou, passa para a próxima tentativa
                if(!win && this.attempt !== 6) {
                  this.guess();
                }
                
              })
              .catch(err => console.error(err));
            }

            else {
              this.handleInvalidWord();
              
            }
          })
          .catch(err => console.error(err));
        
      },

      // Foca no primeiro <input /> da linha
      async focusFirstInput() {
        await nextTick();
        const row = this.$refs.rowComponent.rowRefs[this.attempt];
        if (row) {
          const input = row.querySelector('input');
          if (input) input.focus();
        }
      },

      // Função para lidar com as teclas virtuais
      handleVirtualKey(key) {
        const rowComponent = this.$refs.rowComponent;
        const rowDiv = rowComponent.rowRefs[this.attempt];
        const inputs = rowDiv.querySelectorAll('input');

        // Se pressionar Enter, envia a tentativa
        if (key === 'Enter') {
          this.submitForm();
          return;
        }

        // Se pressionar Backspace, apaga o ultimo input
        if (key === '⌫') {
          for (let i = 4; i >= 0; i--) {
            if (inputs[i].value !== '') {
              inputs[i].value = '';
              inputs[i].focus(); // foca no input apagado
              break;
            }
          }
          return;
        }

        // Se pressionar uma letra, preenche o primeiro input vazio
        for (let i = 0; i < 5; i++) {
          if (inputs[i].value === '') {
            inputs[i].value = key;
            inputs[i].focus(); // foca no input preenchido
            break;
          }
        }
      },
      handleInvalidWord() {
        // Ativa a animação de shake
        this.shakeRow = this.attempt;

        setTimeout(() => {
          this.shakeRow = null;
        }, 500);

        // Limpa os <inputs /> da linha atual
        const rowComponent = this.$refs.rowComponent;

        if (rowComponent?.rowRefs?.[this.attempt]) {
          const rowDiv = rowComponent.rowRefs[this.attempt];
          const inputs = rowDiv.querySelectorAll('input');
          inputs.forEach(input => input.value = '');

          inputs[0].focus();
        }

        return;
      },

      showConfetti() {
        confetti({
          particleCount: 150,
          spread: 70,
          origin: { y: 0.6 },
        });
      },

      // Atualiza a contagem regressiva para o próximo jogo
      updateCountdown() {
        const now = new Date();
        const tomorrow = new Date();
        tomorrow.setHours(24, 0, 0, 0); // meia-noite de hoje pra amanhã

        const diff = tomorrow - now;

        const hours = String(Math.floor(diff / 1000 / 60 / 60)).padStart(2, '0');
        const minutes = String(Math.floor((diff / 1000 / 60) % 60)).padStart(2, '0');
        const seconds = String(Math.floor((diff / 1000) % 60)).padStart(2, '0');

        this.nextGameCountdown = `${hours}:${minutes}:${seconds}`;
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

      // Bloqueia o click fora dos <inputs /> ou do teclado virtual
      window.addEventListener('click', e => {
        e.preventDefault();

        if (e.target.tagName != 'INPUT' && e.target.tagName != 'BUTTON') {
          // Foca no primeiro <input /> da linha ativa
          const rowComponent = this.$refs.rowComponent;
          const rowDiv = rowComponent.rowRefs[this.attempt];
          const inputs = rowDiv.querySelectorAll('input');

          for (let i = 0; i < 5; i++) {
            if (inputs[i].value === '') {
              inputs[i].focus();
              break;
            }
          }
        }

      });
      
      // Contagem regressiva
      this.updateCountdown();
      setInterval(this.updateCountdown, 1000);
    },
  };
</script>