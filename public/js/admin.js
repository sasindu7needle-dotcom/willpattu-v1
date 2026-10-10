const sidebar = document.querySelector('[data-sidebar]');
const toggle = document.querySelector('[data-sidebar-toggle]');
const backdrop = document.querySelector('[data-sidebar-backdrop]');

const closeSidebar = () => {
    document.body.classList.remove('sidebar-open');
    toggle?.setAttribute('aria-expanded', 'false');
};

toggle?.addEventListener('click', () => {
    const isOpen = document.body.classList.toggle('sidebar-open');
    toggle.setAttribute('aria-expanded', String(isOpen));
});

backdrop?.addEventListener('click', closeSidebar);
sidebar?.querySelectorAll('a').forEach((link) => link.addEventListener('click', closeSidebar));

document.querySelectorAll('[data-image-select]').forEach((select) => {
    select.addEventListener('change', () => {
        const preview = document.getElementById(select.dataset.imageTarget);

        if (preview) {
            preview.src = `${select.dataset.imagePrefix}${select.value}${select.dataset.imageSuffix}`;
        }
    });
});

const animationFile = document.querySelector('[data-animation-file]');
const animationPreview = document.querySelector('[data-animation-preview]');
const animationPreviewLabel = document.querySelector('[data-animation-preview-label]');
let animationPreviewUrl = null;

animationFile?.addEventListener('change', () => {
    if (animationPreviewUrl) {
        URL.revokeObjectURL(animationPreviewUrl);
        animationPreviewUrl = null;
    }

    const [file] = animationFile.files;

    if (file && animationPreview) {
        animationPreviewUrl = URL.createObjectURL(file);
        animationPreview.style.backgroundImage = `url("${animationPreviewUrl}")`;
        animationPreviewLabel.textContent = file.name;
    }
});

const confirmDialog = document.querySelector('[data-confirm-dialog]');
const confirmName = confirmDialog?.querySelector('[data-confirm-name]');
const confirmAccept = confirmDialog?.querySelector('[data-confirm-accept]');
let pendingConfirmationForm = null;

document.querySelectorAll('[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        event.preventDefault();
        pendingConfirmationForm = form;
        confirmName.textContent = `“${form.dataset.confirm}”`;
        confirmDialog.showModal();
    });
});

confirmDialog?.querySelectorAll('[data-confirm-cancel]').forEach((button) => {
    button.addEventListener('click', () => confirmDialog.close());
});

confirmAccept?.addEventListener('click', () => {
    const form = pendingConfirmationForm;
    pendingConfirmationForm = null;
    confirmDialog.close();

    if (form) {
        HTMLFormElement.prototype.submit.call(form);
    }
});

confirmDialog?.addEventListener('close', () => {
    pendingConfirmationForm = null;
});

confirmDialog?.addEventListener('click', (event) => {
    const bounds = confirmDialog.getBoundingClientRect();
    const isBackdrop = event.clientX < bounds.left
        || event.clientX > bounds.right
        || event.clientY < bounds.top
        || event.clientY > bounds.bottom;

    if (isBackdrop) {
        confirmDialog.close();
    }
});
