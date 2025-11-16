// App.js - Assets are handled by Vite and Livewire/Flux

// Función patternLock (input interactivo)
function patternLock({ onFinish }) {
    return {
        dotsActive: [],
        isDrawing: false,
        points: [],
        pathEl: null,

        start(event) {
            this.reset();
            this.isDrawing = true;
            const dotId = this.getDotId(event);
            if (dotId) {
                this.addDot(dotId, event);
            }
        },

        move(event) {
            if (!this.isDrawing) return;

            const dotId = this.getDotId(event);

            if (dotId && !this.dotsActive.includes(dotId)) {
                this.addDot(dotId, event);
            } else {
                this.updateTail(event);
            }
        },

        end() {
            if (!this.isDrawing) return;
            this.isDrawing = false;

            const patternString = this.dotsActive.join('-');
            if (typeof onFinish === 'function') {
                onFinish(patternString);
            }
        },

        getDotId(event) {
            const { clientX, clientY } = this.normalize(event);
            const el = document.elementFromPoint(clientX, clientY);
            const dot = el?.closest('[data-id]');
            return dot ? Number(dot.dataset.id) : null;
        },

        addDot(dotId, event) {
            this.dotsActive.push(dotId);

            const { x, y } = this.relative(event);
            this.points.push({ x, y });

            if (!this.pathEl) {
                this.pathEl = this.$refs.path;
                this.pathEl.setAttribute('d', `M ${x},${y}`);
            } else {
                const d = this.pathEl.getAttribute('d') || '';
                this.pathEl.setAttribute('d', `${d} L ${x},${y}`);
            }
        },

        updateTail(event) {
            if (!this.pathEl || this.points.length === 0) return;

            const { x, y } = this.relative(event);

            const base = this.points
                .map(p => `${p.x},${p.y}`)
                .join(' L ');

            this.pathEl.setAttribute('d', `M ${base} L ${x},${y}`);
        },

        normalize(e) {
            if (e.touches?.length) {
                return {
                    clientX: e.touches[0].clientX,
                    clientY: e.touches[0].clientY,
                };
            }
            return {
                clientX: e.clientX,
                clientY: e.clientY,
            };
        },

        relative(e) {
            const { clientX, clientY } = this.normalize(e);
            const rect = e.currentTarget.getBoundingClientRect();
            return {
                x: clientX - rect.left,
                y: clientY - rect.top,
            };
        },

        reset() {
            this.isDrawing = false;
            this.points = [];
            this.dotsActive = [];
            if (this.pathEl) {
                this.pathEl.setAttribute('d', '');
            }
        }
    };
}

// Función patternViewer (solo visualización)
function patternViewer(initialPattern) {
    return {
        pattern: initialPattern || '',
        dots: [],

        isActive(id) {
            return this.dots.includes(id);
        },

        draw() {
            if (!this.pattern) return;

            this.dots = this.pattern
                .split('-')
                .map(n => Number(n))
                .filter(Boolean);

            this.$nextTick(() => {
                const container = this.$el;
                const pathEl = this.$refs.path;
                const dotElements = Array.from(
                    container.querySelectorAll('[data-id]')
                );

                const points = this.dots
                    .map(id => {
                        const el = dotElements.find(d => Number(d.dataset.id) === id);
                        if (!el) return null;

                        const rect = el.getBoundingClientRect();
                        const parentRect = container.getBoundingClientRect();

                        return {
                            x: rect.left + rect.width / 2 - parentRect.left,
                            y: rect.top + rect.height / 2 - parentRect.top,
                        };
                    })
                    .filter(Boolean);

                if (!points.length) return;

                const d = points
                    .map((p, i) => (i === 0 ? `M ${p.x},${p.y}` : `L ${p.x},${p.y}`))
                    .join(' ');

                pathEl.setAttribute('d', d);
            });
        }
    };
}

// Registrar funciones de Alpine
// En Livewire 3, Alpine se carga automáticamente, pero podemos registrar cuando esté disponible
document.addEventListener('alpine:init', () => {
    if (window.Alpine) {
        window.Alpine.data('patternLock', patternLock);
        window.Alpine.data('patternViewer', patternViewer);
    }
});

// También hacer disponible globalmente para uso directo en x-data
window.patternLock = patternLock;
window.patternViewer = patternViewer;
