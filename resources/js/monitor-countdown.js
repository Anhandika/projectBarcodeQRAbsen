export function monitorCountdown({ initial = {}, refreshUrl = '' } = {}) {
    return {
        seconds: Number(initial.countdown_seconds ?? 8),
        qr: initial,
        refreshUrl,
        refreshing: false,
        refreshTimer: null,

        init() {
            this.renderSeconds();
            this.refreshTimer = window.setInterval(() => this.tick(), 1000);
        },

        destroy() {
            window.clearInterval(this.refreshTimer);
        },

        tick() {
            if (this.seconds <= 0) {
                this.refresh();
                return;
            }

            this.seconds -= 1;
        },

        async refresh() {
            if (this.refreshing || ! this.refreshUrl) {
                return;
            }

            this.refreshing = true;

            try {
                const response = await fetch(this.refreshUrl, {
                    headers: { Accept: 'application/json' },
                    credentials: 'same-origin',
                });

                if (! response.ok) {
                    throw new Error('QR belum dapat diperbarui.');
                }

                const payload = await response.json();
                this.qr = payload.qr;
                this.seconds = Number(payload.qr.countdown_seconds ?? 8);
            } catch (error) {
                this.$dispatch('monitor-error', { message: error.message });
                this.seconds = 8;
            } finally {
                this.refreshing = false;
            }
        },

        renderSeconds() {
            return String(Math.max(0, this.seconds)).padStart(2, '0');
        },

        progress() {
            return `${Math.max(0, Math.min(100, (this.seconds / 8) * 100))}%`;
        },
    };
}
