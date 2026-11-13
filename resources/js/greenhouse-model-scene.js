import * as THREE from 'three';
import { GLTFLoader } from 'three/addons/loaders/GLTFLoader.js';
import { OrbitControls } from 'three/addons/controls/OrbitControls.js';

export function createGreenhouseModelScene(container) {
    const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(38, 1, 0.01, 2000);
    const controls = new OrbitControls(camera, renderer.domElement);
    const loading = document.createElement('div');

    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    renderer.outputColorSpace = THREE.SRGBColorSpace;
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    renderer.toneMappingExposure = 1.05;
    renderer.setAnimationLoop(render);
    container.appendChild(renderer.domElement);

    loading.className = 'scene-loading';
    loading.innerHTML = '<span class="scene-loading__bar"></span><strong>Загрузка 3D сцены</strong><small>0%</small>';
    container.appendChild(loading);

    controls.enableDamping = true;
    controls.dampingFactor = 0.06;
    controls.screenSpacePanning = true;

    scene.add(new THREE.HemisphereLight(0xdce8e1, 0x243029, 2.2));
    const keyLight = new THREE.DirectionalLight(0xfff5df, 3.2);
    keyLight.position.set(8, 12, 10);
    scene.add(keyLight);

    new GLTFLoader().load(
        '/models/greenhouse.glb',
        (gltf) => {
            const model = gltf.scene;
            model.traverse((object) => {
                if (!object.isMesh) return;
                object.frustumCulled = true;
                if (object.material) object.material.needsUpdate = true;
            });

            const bounds = new THREE.Box3().setFromObject(model);
            const size = bounds.getSize(new THREE.Vector3());
            const center = bounds.getCenter(new THREE.Vector3());
            const maxSize = Math.max(size.x, size.y, size.z);
            const distance = maxSize / (2 * Math.tan(THREE.MathUtils.degToRad(camera.fov / 2)));

            model.position.sub(center);
            scene.add(model);

            camera.near = Math.max(maxSize / 10000, 0.01);
            camera.far = maxSize * 20;
            camera.position.set(distance * 0.78, distance * 0.42, distance * 0.92);
            camera.updateProjectionMatrix();

            controls.target.set(0, size.y * 0.03, 0);
            controls.minDistance = maxSize * 0.18;
            controls.maxDistance = maxSize * 2.4;
            controls.update();

            loading.classList.add('scene-loading--done');
            setTimeout(() => loading.remove(), 500);
        },
        (event) => {
            if (!event.total) {
                loading.querySelector('small').textContent = 'загрузка…';
                return;
            }

            const progress = Math.round((event.loaded / event.total) * 100);
            loading.querySelector('small').textContent = `${progress}%`;
            loading.querySelector('.scene-loading__bar').style.setProperty('--scene-progress', `${progress}%`);
        },
        (error) => {
            console.error('Не удалось загрузить 3D сцену теплицы', error);
            loading.classList.add('scene-loading--error');
            loading.querySelector('strong').textContent = 'Не удалось загрузить 3D сцену';
            loading.querySelector('small').textContent = 'Проверьте файл public/models/greenhouse.glb';
        },
    );

    const resizeObserver = new ResizeObserver(() => {
        const width = Math.max(container.clientWidth, 1);
        const height = Math.max(container.clientHeight, 1);
        camera.aspect = width / height;
        camera.updateProjectionMatrix();
        renderer.setSize(width, height, false);
    });
    resizeObserver.observe(container);

    function render() {
        controls.update();
        renderer.render(scene, camera);
    }
}
