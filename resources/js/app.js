import './bootstrap';

const copyToClipboard = (text) => {
    if (navigator.clipboard && window.isSecureContext) {
        return navigator.clipboard.writeText(text);
    }

    const textarea = document.createElement('textarea');
    textarea.value = text;
    textarea.style.position = 'fixed';
    textarea.style.left = '-9999px';
    document.body.appendChild(textarea);
    textarea.focus();
    textarea.select();
    document.execCommand('copy');
    textarea.remove();
    return Promise.resolve();
};

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-copy-target]').forEach((button) => {
        button.addEventListener('click', async () => {
            const target = document.querySelector(button.dataset.copyTarget);

            if (!target) {
                return;
            }

            await copyToClipboard(target.value);
            button.textContent = button.dataset.copiedText || 'Copied';
            setTimeout(() => {
                button.textContent = button.dataset.defaultText || button.textContent;
            }, 2000);
        });
    });

    const banner = document.querySelector('[data-cookie-banner]');

    if (banner) {
        const accepted = localStorage.getItem('cookieConsent') === 'true';
        const acceptButton = banner.querySelector('[data-accept-cookies]');

        if (!accepted) {
            banner.classList.remove('hidden');
        }

        acceptButton?.addEventListener('click', () => {
            localStorage.setItem('cookieConsent', 'true');
            banner.remove();
        });
    }
});
