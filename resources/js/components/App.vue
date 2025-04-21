<template>
  <main class="grid justify-center place-content-start h-full md:h-screen overflow-hidden py-4 md:py-8 z-10">
    <section id="game" class="grid justify-center place-content-start gap-8 p-4 md:p-8 z-10 relative">
      <img :src="logo" alt="Termo" class="w-32 md:w-40 h-auto mx-auto" />

      <ErrorMessage :show="showError" />

      <Row 
        ref="rowComponent"
        :attempt="attempt"
        :feedbacks="feedbacks"
        :won-at-row="wonAtRow"
        :shakeRow="shakeRow"
        @ready="onRowReady"
      />
    </section>

    <Keyboard @key="handleVirtualKey" />

    <GameModal
      :show="showModal"
      :title="modalTitle"
      :message="modalMessage"
      :word-of-the-day="correctWord"
      :next-game="nextGameCountdown"
      :result-text="resultText"
    />
  </main>
</template>

<script>
  import { nextTick } from 'vue';
  
  import Row from './Row.vue';
  import Keyboard from './Keyboard.vue';
  import GameModal from './GameModal.vue';
  import ErrorMessage from './ErrorMessage.vue';
  
  import logo from '../../images/logo.png';
  import confetti from 'canvas-confetti';
  
  import axios from 'axios';

  // Pegar o token CSRF
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  export default {
    name: 'App',
    components: { Row, Keyboard, GameModal, ErrorMessage },
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
        resultText: '', // Mensagem de resultado do jogo para compartilhar
        showError: false, // Exibe o erro
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

      async submitForm() {
        // Pegar os inputs
        const game = document.getElementById('game');
        const inputs = game.querySelectorAll('input');
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

        // Verifica se a palavra tem 5 letras
        if (currentWord.length !== 5) return this.focusFirstInput();
        
        // Verifica se a palavra atual existe
        try {
          const checkResponse = await axios.post(`check-word/${currentWord}`, {}, {
            headers: {
              'X-CSRF-TOKEN': csrfToken,
            }
          });

          const exists = checkResponse.data.exists;

          if (exists) {
            const guessResponse = await axios.post('/guess', {
              guess
            }, {
              headers: {
                'X-CSRF-TOKEN': csrfToken,
              }
            });

            this.feedbacks[this.attempt] = guessResponse.data.feedback;

            let win = true;
            for (let i = 1; i <= 5; i++) {
              if (this.feedbacks[this.attempt][i] !== 'correct') {
                win = false;
              }
            }

            if (win) {
              this.wonAtRow = this.attempt;
              this.modalTitle = 'Vitória!';
              this.modalMessage = 'Parabéns, você acertou a palavra do dia!';
              this.correctWord = currentWord;
              this.showModal = true;

              let hasWon = false;
              hasWon = this.handleVictory(hasWon);
              this.resultText = this.generateShareText();
            } else if (this.attempt === 6) {
              this.modalTitle = 'Fim de jogo!';
              this.modalMessage = 'Você usou todas as tentativas.';
              this.correctWord = guessResponse.data.correctWord;
              this.showModal = true;
              this.resultText = this.generateShareText();
            }

            if (!win && this.attempt !== 6) {
              await nextTick() // espera DOM atualizar
              .then(() => this.guess());
            }

            this.saveGameState();
          } else {
            this.handleInvalidWord();
          }

        } catch (err) {
          console.error(err);
        }
        
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

        // Se pressionar Backspace, apaga o ultimo <input />
        if (key === '⌫') {
          for (let i = 4; i >= 0; i--) {
            if (inputs[i].value !== '') {
              inputs[i].value = '';
              inputs[i].focus(); // foca no <input /> apagado
              break;
            }
          }
          return;
        }

        // Se pressionar uma letra, preenche o primeiro <input /> vazio
        for (let i = 0; i < 5; i++) {
          if (inputs[i].value === '') {
            inputs[i].value = key;
            inputs[i].focus(); // foca no <input /> preenchido
            break;
          }
        }
      },
      handleInvalidWord() {
        //
        this.showError = true;

        setTimeout(() => {
          this.showError = false;
        }, 2500);

        // Ativa a animação "shake"
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

      handleVictory(hasWon) {
        if (hasWon) return;
        hasWon = true;

        // Gera o som de vitoria
        const victorySound = new Audio('sounds/victory-horn.mp3');
        victorySound.play();

        // Exibe animação de "confetti"
        this.showConfetti();

        return;
      },

      // Atualiza a contagem regressiva para o próximo jogo
      updateCountdown() {
        const now = new Date();
        const tomorrow = new Date();
        tomorrow.setHours(24, 0, 0, 0); // Reseta à meia-noite

        const diff = tomorrow - now;

        const hours = String(Math.floor(diff / 1000 / 60 / 60)).padStart(2, '0');
        const minutes = String(Math.floor((diff / 1000 / 60) % 60)).padStart(2, '0');
        const seconds = String(Math.floor((diff / 1000) % 60)).padStart(2, '0');

        this.nextGameCountdown = `${hours}:${minutes}:${seconds}`;
      },

      generateShareText() {
        const emojis = {
          correct: '🟩',
          wrong: '⬜',
          present: '🟨',
        };

        let output = `Joguei Termo do dia ${new Date().toLocaleDateString('pt-BR')} em ${this.wonAtRow || 6}/6\n\n`;

        for (let i = 1; i <= (this.wonAtRow || 6); i++) {
          const feedbackRow = this.feedbacks[i];
          for (let j = 1; j <= 5; j++) {
            output += emojis[feedbackRow[j]];
          }
          output += '\n';
        }

        return output;
      },

      // Salva o estado do jogo no localStorage
      saveGameState() {
        // Pegar o conteúdo das letras digitadas
        const game = document.getElementById('game');
        const inputs = game.querySelectorAll('input');
        const letters = {};

        inputs.forEach(input => {
          const [, row, , index] = input.name.split('_'); // row_1_letter_3
          if (!letters[row]) letters[row] = {};
          letters[row][index] = input.value;
        });

        const state = {
          attempt: this.attempt,
          feedbacks: this.feedbacks,
          wonAtRow: this.wonAtRow,
          letters: letters, 
        };

        const todayKey = `termo-${new Date().toISOString().split('T')[0]}`; // exemplo: termo-2025-04-16
        localStorage.setItem(todayKey, JSON.stringify(state));

        
      },

      // Carrega o estado do jogo do localStorage
      loadGameState() {
        const todayKey = `termo-${new Date().toISOString().split('T')[0]}`;
        const saved = localStorage.getItem(todayKey);

        if (saved) {
          const state = JSON.parse(saved);
          this.attempt = state.attempt;
          this.feedbacks = state.feedbacks;
          this.wonAtRow = state.wonAtRow;

          // Preencher <inputs /> com letras restauradas
          this.$nextTick(() => {
            const rowComponent = this.$refs.rowComponent;
            if (!rowComponent?.rowRefs) return;

            Object.keys(state.letters || {}).forEach(rowKey => {
              const rowDiv = rowComponent.rowRefs[rowKey];
              const inputs = rowDiv?.querySelectorAll('input');
              if (!inputs) return;

              for (let i = 0; i < 5; i++) {
                const letter = state.letters[rowKey][i + 1]; // 1-indexed
                if (letter) {
                  inputs[i].value = letter;
                }
              }
            });

            // foca no primeiro input vazio da linha atual
            const rowDiv = rowComponent.rowRefs[this.attempt];
            const inputs = rowDiv?.querySelectorAll('input');
            if (inputs) {
              for (let i = 0; i < 5; i++) {
                if (inputs[i].value === '') {
                  inputs[i].focus();
                  break;
                }
              }
            }
          });
        }

        Object.keys(localStorage).forEach(key => {
          if (key.startsWith('termo-') && key !== todayKey) {
            localStorage.removeItem(key);
          }
        });
      },

      onRowReady() {
        // Após garantir que os refs estão disponíveis, foca no <input />
        const rowComponent = this.$refs.rowComponent;
        const rowDiv = rowComponent?.rowRefs?.[this.attempt];
        if (rowDiv) {
          const inputs = rowDiv.querySelectorAll('input');
          if (inputs.length) inputs[0].focus();
        }
      },
    },
    mounted() {
      this.loadGameState();

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