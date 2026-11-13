import Alpine from 'alpinejs';
import { createPlantsScene } from './greenhouse-scene';
import { createGreenhouseModelScene } from './greenhouse-model-scene';

const themeStorageKey = 'greenhouse-theme';

window.Alpine = Alpine;
Alpine.start();

function applyTheme(theme) {
    document.documentElement.dataset.theme = theme;

    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        button.setAttribute('aria-pressed', String(theme === 'dark'));
        button.setAttribute('aria-label', theme === 'dark' ? 'Включить светлую тему' : 'Включить тёмную тему');
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const savedTheme = localStorage.getItem(themeStorageKey);
    const initialTheme = savedTheme || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');

    applyTheme(initialTheme);

    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const nextTheme = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';

            localStorage.setItem(themeStorageKey, nextTheme);
            applyTheme(nextTheme);
        });
    });

    const greenhouseContainer = document.querySelector('#greenhouse-scene');
    const plantsContainer = document.querySelector('#plants-scene');

    if (greenhouseContainer) createGreenhouseModelScene(greenhouseContainer);
    if (plantsContainer) createPlantsScene(plantsContainer);
});


