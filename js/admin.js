document.addEventListener('DOMContentLoaded', () => {
    const bindPreview = (input, preview) => {
        if (!input || !preview) return;
        input.addEventListener('change', () => {
            const file = input.files && input.files[0];
            if (!file) {
                preview.classList.remove('show');
                return;
            }
            preview.src = URL.createObjectURL(file);
            preview.classList.add('show');
        });
    };

    bindPreview(document.getElementById('agency-image'), document.getElementById('image-preview'));
    bindPreview(document.getElementById('product-image'), document.getElementById('product-preview'));

    document.querySelectorAll('[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (e) => {
            const message = form.getAttribute('data-confirm') || 'Are you sure?';
            if (!window.confirm(message)) e.preventDefault();
        });
    });
});
