import './bootstrap';

const body = document.body;
const root = document.documentElement;
const storedTheme = localStorage.getItem('fitcontrol-theme');
const preferredTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';

const setTheme = (theme) => {
    root.setAttribute('data-bs-theme', theme);
    localStorage.setItem('fitcontrol-theme', theme);

    const icon = document.querySelector('[data-theme-toggle] i');
    if (icon) icon.className = `bi ${theme === 'dark' ? 'bi-sun' : 'bi-moon-stars'}`;
};

setTheme(storedTheme || preferredTheme);

document.querySelector('[data-theme-toggle]')?.addEventListener('click', () => {
    setTheme(root.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark');
});

document.querySelector('[data-sidebar-toggle]')?.addEventListener('click', () => body.classList.add('sidebar-open'));
document.querySelectorAll('[data-sidebar-close]').forEach((element) => element.addEventListener('click', () => body.classList.remove('sidebar-open')));

window.addEventListener('resize', () => {
    if (window.innerWidth >= 992) body.classList.remove('sidebar-open');
});

document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((element) => new window.bootstrap.Tooltip(element));

document.querySelectorAll('form[method]:not([method="GET"]):not([method="get"])').forEach((form) => {
    form.querySelectorAll('input, select, textarea').forEach((field) => {
        if (field.disabled || field.dataset.optional === 'true') return;
        if (['hidden', 'submit', 'button', 'reset', 'file', 'checkbox', 'radio'].includes(field.type)) return;
        if (field.name?.startsWith('_')) return;

        field.required = true;
    });
});
