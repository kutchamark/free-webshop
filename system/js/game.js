(() => {
    const GameArcade = {
        init() {
            this.cache = new Map();
            document.querySelectorAll('[data-game]').forEach(card => {
                try {
                    const payload = JSON.parse(card.getAttribute('data-game') || '{}');
                    this.cache.set(card, payload);
                } catch (err) {
                    console.error('Invalid game payload', err);
                }
            });

            document.addEventListener('click', (event) => {
                const target = event.target.closest('[data-action]');
                if (!target) return;
                const card = target.closest('[data-game]');
                if (!card || !this.cache.has(card)) return;

                const game = this.cache.get(card);
                const action = target.getAttribute('data-action');

                if (action === 'spin') {
                    this.handleSpin(target, card, game);
                } else if (action === 'guess') {
                    this.handleGuess(target, card, game);
                }
            });

            document.querySelectorAll('.rps-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const card = btn.closest('[data-game]');
                    if (!card || !this.cache.has(card)) return;
                    const choice = btn.getAttribute('data-choice');
                    this.handleRps(card, this.cache.get(card), choice, btn);
                });
            });
        },

        handleSpin(button, card, game) {
            if (button.dataset.loading === '1') return;
            button.dataset.loading = '1';
            button.disabled = true;

            this.playGame({
                game_id: game.id,
                action: 'spin'
            }).then((res) => {
                this.animateWheel(card, res.meta?.spin);
                setTimeout(() => {
                    this.presentResult(res);
                    button.dataset.loading = '0';
                    button.disabled = false;
                }, (res.meta?.spin?.duration || 4500) + 200);
            }).catch((err) => {
                this.showError(err);
                button.dataset.loading = '0';
                button.disabled = false;
            });
        },

        animateWheel(card, spinMeta) {
            const wheel = card.querySelector('[data-wheel-graphic]');
            if (!wheel || !spinMeta) return;

            const duration = spinMeta.duration || 4500;
            const rotation = spinMeta.rotation || 1440;
            wheel.style.transition = 'none';
            wheel.style.transform = 'rotate(0deg)';
            // force reflow
            void wheel.offsetWidth;
            wheel.style.transition = `transform ${duration}ms cubic-bezier(.23,1,.32,1)`;
            wheel.style.transform = `rotate(${rotation}deg)`;
        },

        handleGuess(button, card, game) {
            const input = card.querySelector('.guess-input');
            if (!input) return;
            const value = parseInt(input.value, 10);
            if (Number.isNaN(value)) {
                Swal.fire('กรุณากรอกตัวเลข', 'ใส่เลขให้ครบก่อนกดส่งคำตอบ', 'info');
                return;
            }
            if (value < game.min || value > game.max) {
                Swal.fire('เลขอยู่นอกช่วง', `กรุณาเลือกเลขระหว่าง ${game.min} - ${game.max}`, 'warning');
                return;
            }
            if (button.dataset.loading === '1') return;
            button.dataset.loading = '1';
            button.disabled = true;

            this.playGame({
                game_id: game.id,
                action: 'guess',
                guess: value
            }).then((res) => {
                this.presentResult(res);
                button.dataset.loading = '0';
                button.disabled = false;
            }).catch((err) => {
                this.showError(err);
                button.dataset.loading = '0';
                button.disabled = false;
            });
        },

        handleRps(card, game, choice, button) {
            if (!choice) return;
            if (button.dataset.loading === '1') return;
            button.dataset.loading = '1';
            this.toggleRpsButtons(card, true);

            this.playGame({
                game_id: game.id,
                action: 'rps',
                choice
            }).then((res) => {
                this.presentResult(res);
                this.toggleRpsButtons(card, false);
                button.dataset.loading = '0';
            }).catch((err) => {
                this.showError(err);
                this.toggleRpsButtons(card, false);
                button.dataset.loading = '0';
            });
        },

        toggleRpsButtons(card, disable) {
            card.querySelectorAll('.rps-btn').forEach(btn => {
                btn.disabled = disable;
                btn.classList.toggle('is-loading', disable);
            });
        },

        playGame(payload) {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: 'system/game/play.php',
                    data: payload,
                    dataType: 'json'
                }).done((res) => {
                    if (typeof res.balance !== 'undefined') {
                        this.updateBalance(res.balance);
                    }
                    resolve(res);
                }).fail((xhr) => {
                    const msg = xhr.responseJSON?.message || 'ไม่สามารถเล่นเกมได้';
                    reject(msg);
                });
            });
        },

        presentResult(res) {
            const reward = res.reward || {};
            const meta = res.meta || {};
            const lines = [];
            if (meta.system_value !== undefined) {
                lines.push(`ผลสุ่ม: ${meta.system_value}`);
            }
            if (meta.choice_value !== undefined) {
                lines.push(`ตัวเลือกของคุณ: ${meta.choice_value}`);
            }
            if (reward.detail) {
                lines.push(reward.detail);
            }
            const text = lines.join('\n');
            const icon = reward.type === 'points' ? 'success' : 'info';
            Swal.fire({
                title: reward.label || 'ผลลัพธ์',
                text,
                icon
            });
        },

        updateBalance(amount) {
            const display = document.getElementById('currentPointBalance');
            if (!display) return;
            const formatted = new Intl.NumberFormat('th-TH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(parseFloat(amount));
            display.textContent = `฿${formatted}`;
            display.dataset.currentPoint = formatted;
        },

        showError(message) {
            Swal.fire('เกิดข้อผิดพลาด', message || 'ลองใหม่อีกครั้ง', 'error');
        }
    };

    if (window.jQuery) {
        jQuery(() => GameArcade.init());
    } else {
        document.addEventListener('DOMContentLoaded', () => GameArcade.init());
    }
})();
