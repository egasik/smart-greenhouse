import * as THREE from 'three';

const colors = {
    glass: 0x93d7d1,
    frame: 0x293b39,
    ground: 0x18201c,
    soil: 0x4b3325,
    leaf: 0x3d8c57,
    leafLight: 0x67b978,
    tomato: 0xd55242,
    pepper: 0x4ea35c,
    metal: 0x9db2a9,
    light: 0xffe2a8,
};

function createRenderer(container) {
    const renderer = new THREE.WebGLRenderer({
        antialias: true,
        alpha: true,
    });

    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    renderer.outputColorSpace = THREE.SRGBColorSpace;
    renderer.setSize(container.clientWidth, container.clientHeight);

    container.appendChild(renderer.domElement);

    return renderer;
}

function addBox(group, size, position, color, opacity = 1) {
    const geometry = new THREE.BoxGeometry(...size);
    const material = new THREE.MeshStandardMaterial({
        color,
        transparent: opacity < 1,
        opacity,
        roughness: 0.55,
        metalness: 0.15,
    });

    const mesh = new THREE.Mesh(geometry, material);
    mesh.position.set(...position);
    group.add(mesh);

    return mesh;
}

function addCylinder(group, radius, height, position, color) {
    const geometry = new THREE.CylinderGeometry(radius, radius, height, 10);
    const material = new THREE.MeshStandardMaterial({
        color,
        roughness: 0.7,
    });

    const mesh = new THREE.Mesh(geometry, material);
    mesh.position.set(...position);
    group.add(mesh);

    return mesh;
}

function addLeaf(group, position, rotation, scale, color = colors.leaf) {
    const geometry = new THREE.SphereGeometry(0.32, 12, 10);
    const material = new THREE.MeshStandardMaterial({
        color,
        roughness: 0.8,
    });

    const leaf = new THREE.Mesh(geometry, material);
    leaf.position.set(...position);
    leaf.rotation.set(...rotation);
    leaf.scale.set(...scale);
    group.add(leaf);
}

function createTomatoPlant(position) {
    const plant = new THREE.Group();

    addCylinder(plant, 0.06, 2.1, [0, 1.05, 0], colors.leaf);

    const leafData = [
        [[0.34, 1.45, 0], [0.1, 0, -0.65], [1.25, 0.34, 0.6]],
        [[-0.34, 1.18, 0], [-0.1, 0, 0.65], [1.25, 0.34, 0.6]],
        [[0.26, 0.85, 0.08], [0.1, 0.2, -0.6], [1.1, 0.3, 0.55]],
        [[-0.24, 1.72, 0], [-0.2, 0, 0.55], [1.05, 0.3, 0.5]],
    ];

    leafData.forEach(([leafPosition, rotation, scale], index) => {
        addLeaf(
            plant,
            leafPosition,
            rotation,
            scale,
            index % 2 === 0 ? colors.leaf : colors.leafLight
        );
    });

    [
        [0.24, 1.03, 0.17],
        [-0.18, 1.31, 0.1],
        [0.12, 1.56, -0.14],
    ].forEach((fruitPosition) => {
        const fruit = new THREE.Mesh(
            new THREE.SphereGeometry(0.14, 14, 14),
            new THREE.MeshStandardMaterial({
                color: colors.tomato,
                roughness: 0.5,
            })
        );

        fruit.position.set(...fruitPosition);
        plant.add(fruit);
    });

    plant.position.set(...position);

    return plant;
}

function createPepperPlant(position) {
    const plant = new THREE.Group();

    addCylinder(plant, 0.06, 1.75, [0, 0.88, 0], colors.leaf);

    [
        [[0.28, 1.35, 0], [0.1, 0, -0.6], [1.35, 0.3, 0.58]],
        [[-0.28, 1.07, 0], [-0.1, 0, 0.6], [1.35, 0.3, 0.58]],
        [[0.2, 0.75, 0.1], [0.2, 0.15, -0.5], [1.1, 0.28, 0.5]],
        [[-0.18, 1.6, 0], [-0.2, 0, 0.5], [1.1, 0.28, 0.5]],
    ].forEach(([leafPosition, rotation, scale], index) => {
        addLeaf(
            plant,
            leafPosition,
            rotation,
            scale,
            index % 2 === 0 ? colors.leaf : colors.leafLight
        );
    });

    [
        [0.18, 0.88, 0.13],
        [-0.16, 1.18, 0.1],
    ].forEach((fruitPosition) => {
        const fruit = new THREE.Mesh(
            new THREE.CapsuleGeometry(0.11, 0.22, 6, 12),
            new THREE.MeshStandardMaterial({
                color: colors.pepper,
                roughness: 0.45,
            })
        );

        fruit.rotation.z = 0.15;
        fruit.position.set(...fruitPosition);
        plant.add(fruit);
    });

    plant.position.set(...position);

    return plant;
}

function createLettuce(position) {
    const plant = new THREE.Group();

    for (let index = 0; index < 11; index += 1) {
        const angle = (Math.PI * 2 * index) / 11;
        const radius = index % 2 === 0 ? 0.36 : 0.24;

        addLeaf(
            plant,
            [
                Math.cos(angle) * radius,
                0.2 + (index % 3) * 0.04,
                Math.sin(angle) * radius,
            ],
            [0.38, angle, Math.sin(angle) * 0.25],
            [1.05, 0.32, 0.55],
            index % 2 === 0 ? colors.leafLight : colors.leaf
        );
    }

    plant.position.set(...position);

    return plant;
}

function createPlantPot(position, plant) {
    const group = new THREE.Group();

    const pot = new THREE.Mesh(
        new THREE.CylinderGeometry(0.48, 0.38, 0.62, 20),
        new THREE.MeshStandardMaterial({
            color: 0x805b42,
            roughness: 0.9,
        })
    );

    pot.position.y = 0.31;
    group.add(pot);

    const soil = new THREE.Mesh(
        new THREE.CylinderGeometry(0.42, 0.42, 0.04, 20),
        new THREE.MeshStandardMaterial({
            color: colors.soil,
            roughness: 1,
        })
    );

    soil.position.y = 0.63;
    group.add(soil);

    group.add(plant);
    group.position.set(...position);

    return group;
}

function addLights(scene) {
    const keyLight = new THREE.DirectionalLight(0xd5f5e1, 2.4);
    keyLight.position.set(5, 7, 6);
    scene.add(keyLight);

    const fillLight = new THREE.DirectionalLight(0x5ea0cc, 1.2);
    fillLight.position.set(-6, 3, -4);
    scene.add(fillLight);

    const warmLight = new THREE.PointLight(colors.light, 14, 8);
    warmLight.position.set(0, 3.2, 0);
    scene.add(warmLight);

    scene.add(new THREE.AmbientLight(0x9cbab4, 1.5));
}

export function createGreenhouseScene(container) {
    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(
        32,
        container.clientWidth / container.clientHeight,
        0.1,
        100
    );

    const renderer = createRenderer(container);
    const greenhouse = new THREE.Group();
    const targetRotation = { x: -0.13, y: -0.55 };

    camera.position.set(7.8, 5.4, 10.5);
    camera.lookAt(0, 1.6, 0);

    addLights(scene);
    scene.add(greenhouse);

    const ground = new THREE.Mesh(
        new THREE.CircleGeometry(5, 48),
        new THREE.MeshStandardMaterial({
            color: colors.ground,
            roughness: 1,
            transparent: true,
            opacity: 0.9,
        })
    );

    ground.rotation.x = -Math.PI / 2;
    scene.add(ground);

    addBox(greenhouse, [6.8, 0.24, 4.4], [0, 0.12, 0], colors.frame);

    const glassMaterial = new THREE.MeshPhysicalMaterial({
        color: colors.glass,
        transparent: true,
        opacity: 0.16,
        roughness: 0.1,
        metalness: 0,
        side: THREE.DoubleSide,
    });

    const frontPanel = new THREE.Mesh(
        new THREE.PlaneGeometry(6.2, 3.5),
        glassMaterial
    );

    frontPanel.position.set(0, 1.9, 2.05);
    greenhouse.add(frontPanel);

    const sidePanel = new THREE.Mesh(
        new THREE.PlaneGeometry(4.1, 3.5),
        glassMaterial
    );

    sidePanel.rotation.y = Math.PI / 2;
    sidePanel.position.set(3.1, 1.9, 0);
    greenhouse.add(sidePanel);

    const roofLeft = new THREE.Mesh(
        new THREE.PlaneGeometry(3.4, 4.2),
        glassMaterial
    );

    roofLeft.rotation.set(Math.PI / 4, 0, 0);
    roofLeft.position.set(-1.55, 4.05, 0);
    greenhouse.add(roofLeft);

    const roofRight = new THREE.Mesh(
        new THREE.PlaneGeometry(3.4, 4.2),
        glassMaterial
    );

    roofRight.rotation.set(-Math.PI / 4, 0, 0);
    roofRight.position.set(1.55, 4.05, 0);
    greenhouse.add(roofRight);

    const framePositions = [
        [-3.1, 1.8, -2.05],
        [3.1, 1.8, -2.05],
        [-3.1, 1.8, 2.05],
        [3.1, 1.8, 2.05],
        [-3.1, 3.6, 0],
        [3.1, 3.6, 0],
    ];

    framePositions.forEach((position) => {
        addBox(greenhouse, [0.11, 3.6, 0.11], position, colors.frame);
    });

    addBox(greenhouse, [6.3, 0.12, 0.12], [0, 3.6, -2.05], colors.frame);
    addBox(greenhouse, [6.3, 0.12, 0.12], [0, 3.6, 2.05], colors.frame);
    addBox(greenhouse, [0.12, 0.12, 4.1], [-3.1, 3.6, 0], colors.frame);
    addBox(greenhouse, [0.12, 0.12, 4.1], [3.1, 3.6, 0], colors.frame);

    const roofFrameLeft = addBox(
        greenhouse,
        [0.12, 0.12, 4.3],
        [-1.55, 4.8, 0],
        colors.frame
    );

    roofFrameLeft.rotation.x = Math.PI / 4;

    const roofFrameRight = addBox(
        greenhouse,
        [0.12, 0.12, 4.3],
        [1.55, 4.8, 0],
        colors.frame
    );

    roofFrameRight.rotation.x = -Math.PI / 4;

    const sensor = new THREE.Mesh(
        new THREE.BoxGeometry(0.38, 0.5, 0.2),
        new THREE.MeshStandardMaterial({
            color: colors.metal,
            roughness: 0.35,
            metalness: 0.3,
        })
    );

    sensor.position.set(1.8, 2.3, 1.4);
    greenhouse.add(sensor);

    const sensorDot = new THREE.Mesh(
        new THREE.SphereGeometry(0.045, 12, 12),
        new THREE.MeshStandardMaterial({
            color: colors.light,
            emissive: colors.light,
            emissiveIntensity: 1.2,
        })
    );

    sensorDot.position.set(1.8, 2.3, 1.53);
    greenhouse.add(sensorDot);

    greenhouse.add(createPlantPot([-1.5, 0.24, 0.8], createTomatoPlant([0, 0.63, 0])));
    greenhouse.add(createPlantPot([0, 0.24, -0.6], createLettuce([0, 0.63, 0])));
    greenhouse.add(createPlantPot([1.6, 0.24, 0.7], createPepperPlant([0, 0.63, 0])));

    greenhouse.rotation.set(targetRotation.x, targetRotation.y, 0);
    greenhouse.position.y = -0.4;

    const resizeObserver = new ResizeObserver(() => {
        const width = container.clientWidth;
        const height = container.clientHeight;

        camera.aspect = width / height;
        camera.updateProjectionMatrix();
        renderer.setSize(width, height);
    });

    resizeObserver.observe(container);

    container.addEventListener('pointermove', (event) => {
        const bounds = container.getBoundingClientRect();
        const pointerX = (event.clientX - bounds.left) / bounds.width;
        const pointerY = (event.clientY - bounds.top) / bounds.height;

        targetRotation.y = -0.9 + pointerX * 0.65;
        targetRotation.x = -0.24 + pointerY * 0.18;
    });

    container.addEventListener('pointerleave', () => {
        targetRotation.x = -0.13;
        targetRotation.y = -0.55;
    });

    function animate() {
        greenhouse.rotation.x += (targetRotation.x - greenhouse.rotation.x) * 0.04;
        greenhouse.rotation.y += (targetRotation.y - greenhouse.rotation.y) * 0.04;

        renderer.render(scene, camera);
        requestAnimationFrame(animate);
    }

    animate();
}

export function createPlantsScene(container) {
    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(
        31,
        container.clientWidth / container.clientHeight,
        0.1,
        100
    );

    const renderer = createRenderer(container);
    const plants = new THREE.Group();
    const targetRotation = { y: -0.22 };

    camera.position.set(5.4, 3.5, 8.5);
    camera.lookAt(0, 1.4, 0);

    addLights(scene);
    scene.add(plants);

    plants.add(createPlantPot([-1.55, 0, 0], createTomatoPlant([0, 0.63, 0])));
    plants.add(createPlantPot([0, 0, 0.15], createLettuce([0, 0.63, 0])));
    plants.add(createPlantPot([1.55, 0, 0], createPepperPlant([0, 0.63, 0])));

    const resizeObserver = new ResizeObserver(() => {
        const width = container.clientWidth;
        const height = container.clientHeight;

        camera.aspect = width / height;
        camera.updateProjectionMatrix();
        renderer.setSize(width, height);
    });

    resizeObserver.observe(container);

    container.addEventListener('pointermove', (event) => {
        const bounds = container.getBoundingClientRect();
        const pointerX = (event.clientX - bounds.left) / bounds.width;

        targetRotation.y = -0.75 + pointerX * 1.1;
    });

    container.addEventListener('pointerleave', () => {
        targetRotation.y = -0.22;
    });

    function animate() {
        plants.rotation.y += (targetRotation.y - plants.rotation.y) * 0.04;

        renderer.render(scene, camera);
        requestAnimationFrame(animate);
    }

    animate();
}





