import * as THREE from 'three';
import { OrbitControls } from 'three/addons/controls/OrbitControls.js';
import { GLTFLoader } from 'three/addons/loaders/GLTFLoader.js';
import { RoomEnvironment } from 'three/addons/environments/RoomEnvironment.js';

const MODEL_URL = '/models/smart-greenhouse-hero.glb';

function setShadows(root) {
    root.traverse((node) => {
        if (!node.isMesh) return;
        node.castShadow = true;
        node.receiveShadow = true;

        if (node.material?.transparent) {
            node.material.depthWrite = false;
        }
    });
}

function createStage(container) {
    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(34, 1, 0.1, 100);
    const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });

    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    renderer.setSize(container.clientWidth, container.clientHeight);
    renderer.shadowMap.enabled = true;
    renderer.shadowMap.type = THREE.PCFSoftShadowMap;
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    renderer.toneMappingExposure = 1.16;
    renderer.outputColorSpace = THREE.SRGBColorSpace;
    renderer.domElement.setAttribute('aria-label', 'Интерактивная 3D-модель умной теплицы');
    container.replaceChildren(renderer.domElement);

    const pmrem = new THREE.PMREMGenerator(renderer);
    scene.environment = pmrem.fromScene(new RoomEnvironment(), 0.035).texture;
    pmrem.dispose();

    const keyLight = new THREE.DirectionalLight(0xd9ffe8, 3.5);
    keyLight.position.set(5, 8, 5);
    keyLight.castShadow = true;
    keyLight.shadow.mapSize.set(2048, 2048);
    keyLight.shadow.camera.near = 0.5;
    keyLight.shadow.camera.far = 22;
    keyLight.shadow.normalBias = 0.025;
    scene.add(keyLight);

    const fillLight = new THREE.DirectionalLight(0x6bbdd0, 1.7);
    fillLight.position.set(-6, 3, -5);
    scene.add(fillLight);

    const plantLight = new THREE.PointLight(0xff7a2f, 8, 12, 2);
    plantLight.position.set(0, 3.8, 0);
    scene.add(plantLight);

    const ground = new THREE.Mesh(
        new THREE.CircleGeometry(7.5, 64),
        new THREE.ShadowMaterial({ color: 0x000000, opacity: 0.34 })
    );
    ground.rotation.x = -Math.PI / 2;
    ground.position.y = -0.02;
    ground.receiveShadow = true;
    scene.add(ground);

    const controls = new OrbitControls(camera, renderer.domElement);
    controls.enableDamping = true;
    controls.dampingFactor = 0.055;
    controls.enablePan = false;
    controls.minDistance = 8.4;
    controls.maxDistance = 14;
    controls.minPolarAngle = Math.PI * 0.28;
    controls.maxPolarAngle = Math.PI * 0.47;
    controls.minAzimuthAngle = -1.05;
    controls.maxAzimuthAngle = 0.72;
    controls.target.set(0, 0, 1.85);
    camera.position.set(8.4, -9.5, 5.9);
    controls.update();

    const resizeObserver = new ResizeObserver(() => {
        const width = Math.max(container.clientWidth, 1);
        const height = Math.max(container.clientHeight, 1);
        camera.aspect = width / height;
        camera.updateProjectionMatrix();
        renderer.setSize(width, height, false);
    });
    resizeObserver.observe(container);

    return { scene, camera, renderer, controls, resizeObserver };
}

async function addModel(scene, { plantsOnly = false } = {}) {
    const loader = new GLTFLoader();
    const gltf = await loader.loadAsync(MODEL_URL);
    const model = gltf.scene;

    setShadows(model);

    if (plantsOnly) {
        model.traverse((node) => {
            const hidden = /Foundation|frame|glass|Roof|Growing bed|Soil in growing bed|LED grow light|sensor/i;
            if (hidden.test(node.name)) node.visible = false;
        });
        model.position.y = -0.5;
        model.scale.setScalar(1.35);
    }

    scene.add(model);
    return model;
}

function runLoop({ renderer, scene, camera, controls, container, resizeObserver, idleRotate = true }) {
    let frameId;
    let active = true;
    let lastInteraction = performance.now();
    const clock = new THREE.Clock();

    const markInteraction = () => {
        lastInteraction = performance.now();
    };
    container.addEventListener('pointerdown', markInteraction);
    container.addEventListener('wheel', markInteraction, { passive: true });

    const observer = new IntersectionObserver(([entry]) => {
        active = entry.isIntersecting;
    }, { threshold: 0.05 });
    observer.observe(container);

    const render = () => {
        frameId = requestAnimationFrame(render);
        if (!active) return;

        const delta = Math.min(clock.getDelta(), 0.05);
        controls.autoRotate = idleRotate && performance.now() - lastInteraction > 2600;
        controls.autoRotateSpeed = 0.32;
        controls.update(delta);
        renderer.render(scene, camera);
    };
    render();

    return () => {
        cancelAnimationFrame(frameId);
        resizeObserver.disconnect();
        observer.disconnect();
        controls.dispose();
        renderer.dispose();
    };
}

async function createScene(container, options) {
    if (!container || container.dataset.sceneReady === 'true') return;
    container.dataset.sceneReady = 'true';
    container.setAttribute('aria-busy', 'true');

    const stage = createStage(container);
    try {
        await addModel(stage.scene, options);
        container.setAttribute('aria-busy', 'false');
        return runLoop({ ...stage, container, idleRotate: options.idleRotate });
    } catch (error) {
        console.error('Не удалось загрузить 3D-модель теплицы:', error);
        container.dataset.sceneReady = 'false';
        container.setAttribute('aria-busy', 'false');
        container.innerHTML = '<p class="scene-error">3D-модель не загрузилась. Проверьте файл public/models/smart-greenhouse-hero.glb</p>';
        stage.resizeObserver.disconnect();
        stage.renderer.dispose();
    }
}

export function createGreenhouseScene(container) {
    return createScene(container, { plantsOnly: false, idleRotate: true });
}

export function createPlantsScene(container) {
    return createScene(container, { plantsOnly: true, idleRotate: false });
}
