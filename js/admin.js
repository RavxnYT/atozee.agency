document.addEventListener('DOMContentLoaded', () => {
    const fileInput = document.querySelector('input[name="image"]');
    const preview = document.getElementById('image-preview');
    if (fileInput && preview) {
        fileInput.addEventListener('change', () => {
            const file = fileInput.files && fileInput.files[0];
            if (!file) {
                preview.classList.remove('show');
                return;
            }
            preview.src = URL.createObjectURL(file);
            preview.classList.add('show');
        });
    }

    document.querySelectorAll('[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (e) => {
            const message = form.getAttribute('data-confirm') || 'Are you sure?';
            if (!window.confirm(message)) e.preventDefault();
        });
    });
});
