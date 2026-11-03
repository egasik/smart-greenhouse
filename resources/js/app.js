import './bootstrap';
import { createGreenhouseScene, createPlantsScene } from './greenhouse-scene';

document.addEventListener('DOMContentLoaded', () => {
    const greenhouseContainer = document.querySelector('#greenhouse-scene');
    const plantsContainer = document.querySelector('#plants-scene');

    if (greenhouseContainer) {
        createGreenhouseScene(greenhouseContainer);
    }

    if (plantsContainer) {
        createPlantsScene(plantsContainer);
    }
});