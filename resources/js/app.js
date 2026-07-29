import Sortable from 'sortablejs';
window.Sortable = Sortable;

Alpine.data('quillField', ({ editorId, wireModel, dir }) => ({
    quill: null,
    initialized: false,

    async init() {
        if (this.initialized) return;

        this.initialized = true;

        const [{ default: Quill }] = await Promise.all([
            import('quill'),
            import('quill/dist/quill.snow.css'),
        ]);

        this.$nextTick(() => {
            const el = document.getElementById(editorId);
            if (!el) return;
            el.innerHTML = '';

            this.quill = new Quill(el, {
                theme: 'snow',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline'],
                        [{ list: 'ordered' }, { list: 'bullet' }],
                        [{ align: [] }],
                    ],
                },
            });

            const editor = el.querySelector('.ql-editor');
            editor.setAttribute('dir', dir);
            editor.style.textAlign = dir === 'rtl' ? 'right' : 'left';

            // ✅ سجّل الـ listener الأول - قبل أي محاولة تحميل محتوى
            this.quill.on('text-change', () => {
                const html = this.quill.root.innerHTML;
                this.$wire.set(
                    wireModel,
                    html === '<p><br></p>' ? '' : html
                );
            });

            // ✅ حمّل المحتوى الابتدائي بطريقة آمنة (من غير dangerouslyPasteHTML)
            // ومؤجل لفريم كامل عشان الـ toolbar/selection يخلصوا تركيب
            requestAnimationFrame(() => {
                const initial = this.$wire.get(wireModel);
                if (initial) {
                    try {
                        const delta = this.quill.clipboard.convert({ html: initial });
                        this.quill.setContents(delta, 'silent');
                    } catch (e) {
                        console.error('Quill initial content load failed:', e);
                    }
                }
            });
        });
    },

    destroy() {
        if (this.quill) {
            this.quill.off('text-change');
            this.quill = null;
        }
        this.initialized = false;
    }
}));
