import { createGreenhouseScene, createPlantsScene } from './greenhouse-scene';

document.addEventListener('DOMContentLoaded', () => {
    createGreenhouseScene(document.querySelector('#greenhouse-scene'));
    createPlantsScene(document.querySelector('#plants-scene'));
});


