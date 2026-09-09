<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Convención, Murder Mystery & La Venganza de Valentín (PHP Edition)</title>
    <style>
        body {
            margin: 0; overflow: hidden;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #020204; color: #fff;
            user-select: none; touch-action: none;
        }

        .overlay-screen {
            position: absolute; top: 0; left: 0; width: 100vw; height: 100vh;
            background: radial-gradient(circle, #1a0826 0%, #000000 100%);
            display: flex; flex-direction: column; justify-content: center; align-items: center;
            z-index: 100;
        }

        h1 {
            font-size: 2.2rem; margin-bottom: 5px; text-align: center;
            color: #ff2a6d; text-shadow: 0 0 20px rgba(255, 42, 109, 0.8);
            letter-spacing: 2px; text-transform: uppercase;
        }

        .cards-container { display: flex; gap: 15px; margin-top: 15px; flex-wrap: wrap; justify-content: center; }

        .card {
            background: rgba(255, 255, 255, 0.04);
            border: 2px solid rgba(255, 42, 109, 0.3);
            border-radius: 15px; padding: 18px; width: 180px;
            text-align: center; cursor: pointer; transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .card:hover, .card:active {
            transform: translateY(-5px) scale(1.02);
            border-color: #05d9e8; box-shadow: 0 0 25px rgba(5, 217, 232, 0.5);
        }

        #mobile-controls {
            position: absolute; bottom: 20px; left: 0; width: 100vw; height: 140px;
            pointer-events: none; z-index: 50; display: none; justify-content: space-between;
            padding: 0 25px; box-sizing: border-box;
        }

        #touch-joystick {
            width: 110px; height: 110px; background: rgba(255,255,255,0.15);
            border: 2px solid rgba(255,255,255,0.3); border-radius: 50%;
            pointer-events: auto; position: relative; touch-action: none;
        }

        #joystick-stick {
            width: 45px; height: 45px; background: #05d9e8;
            border-radius: 50%; position: absolute; top: 32px; left: 32px;
            box-shadow: 0 0 15px #05d9e8;
        }

        .mobile-btn {
            width: 75px; height: 75px; background: rgba(255, 42, 109, 0.7);
            border: 2px solid #ff2a6d; border-radius: 50%; pointer-events: auto;
            display: flex; justify-content: center; align-items: center;
            font-size: 0.9rem; font-weight: bold; color: #fff;
            box-shadow: 0 0 15px rgba(255, 42, 109, 0.5);
        }

        #ui-layer {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            pointer-events: none; display: none; flex-direction: column;
            justify-content: space-between; padding: 15px; box-sizing: border-box; z-index: 5;
        }

        .hud-top { display: flex; justify-content: space-between; align-items: flex-start; }

        #fury-container {
            width: 220px; background: rgba(5, 5, 10, 0.85);
            border: 2px solid #ff2a6d; border-radius: 10px; padding: 10px; backdrop-filter: blur(8px);
        }

        #fury-bar-bg { width: 100%; height: 14px; background-color: #111; border-radius: 5px; overflow: hidden; margin-top: 5px; }
        #fury-bar { width: 0%; height: 100%; background: linear-gradient(90deg, #ffc800, #ff2a6d, #ff0000); transition: width 0.3s ease; }

        #score-board {
            background: rgba(5, 5, 10, 0.85); padding: 10px 15px;
            border-radius: 10px; border-right: 4px solid #05d9e8; text-align: right;
        }

        #car-display-card {
            position: absolute; top: 80px; right: 15px;
            width: 220px; background: rgba(10, 10, 18, 0.9);
            border: 1px solid rgba(255,255,255,0.15); border-radius: 12px;
            overflow: hidden; backdrop-filter: blur(10px);
        }

        #car-image { width: 100%; height: 110px; object-fit: cover; display: block; }
        #car-details { padding: 10px; }

        #car-hint {
            display: block; margin-top: 4px; font-size: 0.7rem;
            color: #ffea00; font-style: italic; background: rgba(255,234,0,0.1);
            padding: 3px 6px; border-radius: 4px; border-left: 3px solid #ffea00;
        }

        #rating-controls {
            pointer-events: auto; align-self: center; margin-bottom: 10px;
            display: flex; gap: 8px; background: rgba(5, 5, 10, 0.9);
            padding: 10px 18px; border-radius: 30px; border: 1px solid rgba(255, 42, 109, 0.3);
        }

        .btn-rate {
            background: #12121a; border: 2px solid #333; color: white;
            padding: 8px 14px; font-size: 0.8rem; font-weight: bold; border-radius: 20px; cursor: pointer;
        }

        #role-badge {
            position: absolute; top: 15px; left: 50%; transform: translateX(-50%);
            font-size: 1.5rem; font-weight: 900; letter-spacing: 2px;
            padding: 6px 20px; border-radius: 12px; text-transform: uppercase;
            text-shadow: 0 0 15px currentColor; display: none; z-index: 10;
        }

        #fps-hud {
            position: absolute; top: 0; left: 0; width: 100vw; height: 100vh;
            pointer-events: none; display: none; flex-direction: column;
            justify-content: space-between; align-items: center; padding: 20px; box-sizing: border-box; z-index: 8;
        }

        #survival-timer {
            font-size: 2rem; font-weight: bold; color: #ff2a6d;
            text-shadow: 0 0 15px #ff2a6d; background: rgba(0,0,0,0.8);
            padding: 6px 20px; border-radius: 10px; border: 2px solid #ff2a6d;
        }

        #city-hud { font-size: 1.2rem; font-weight: 900; text-align: center; display: none; color: #05d9e8; }

        #cinematic-subtitle {
            position: absolute; bottom: 80px; width: 100%; text-align: center;
            font-size: 2rem; font-weight: 900; color: #ff0055; text-shadow: 0 0 20px #ff0055;
            display: none; z-index: 15; pointer-events: none;
        }

        #game-over {
            position: absolute; top: 0; left: 0; width: 100vw; height: 100vh;
            background: rgba(10, 0, 0, 0.95); display: none;
            flex-direction: column; justify-content: center; align-items: center; z-index: 100;
        }

        .btn-group-end { display: flex; gap: 15px; margin-top: 20px; }
        .btn-end { padding: 12px 24px; font-size: 1rem; font-weight: bold; color: white; border: none; border-radius: 8px; cursor: pointer; }
    </style>
</head>
<body>

    <!-- DISPOSITIVO -->
    <div id="device-select" class="overlay-screen">
        <h1>SELECCIONA TU DISPOSITIVO</h1>
        <div class="cards-container">
            <div class="card" onclick="setDevice('pc')">
                <h2 style="color:#05d9e8;">PC / LAPTOP</h2>
                <p>Teclado (WASD) y Ratón.</p>
            </div>
            <div class="card" onclick="setDevice('mobile')">
                <h2 style="color:#ff2a6d;">MÓVIL / CELULAR</h2>
                <p>Pantalla táctil con joystick.</p>
            </div>
        </div>
    </div>

    <!-- MODO -->
    <div id="character-select" class="overlay-screen" style="display:none;">
        <h1>MODO DE JUEGO PHP</h1>
        <div class="cards-container">
            <div class="card" onclick="startGame('Joako', 'classic')">
                <h2 style="color:#05d9e8;">CONVENCIÓN</h2>
                <p>Acompaña a <strong>Joako</strong>.</p>
            </div>
            <div class="card" onclick="startGame('Philippe', 'classic')">
                <h2 style="color:#ff2a6d;">CONVENCIÓN</h2>
                <p>Acompaña a <strong>Philippe</strong>.</p>
            </div>
            <div class="card" onclick="startGame('Joako', 'murder')" style="border-color:#ffea00;">
                <h2 style="color:#ffea00;">MURDER MYSTERY</h2>
                <p>Cabaña, bosque y roles.</p>
            </div>
        </div>
    </div>

    <!-- CONTROLES TÁCTILES -->
    <div id="mobile-controls">
        <div id="touch-joystick"><div id="joystick-stick"></div></div>
        <div class="mobile-btn" onclick="triggerMobileAction()">ACCIÓN</div>
    </div>

    <!-- HUD CONVENCIÓN -->
    <div id="ui-layer">
        <div class="hud-top">
            <div id="fury-container">
                <div style="display:flex; justify-content:space-between; font-size:0.75rem; color:#ff2a6d; font-weight:bold;">
                    <span>FURIA VALENTÍN</span>
                    <span id="fury-percentage">0%</span>
                </div>
                <div id="fury-bar-bg"><div id="fury-bar"></div></div>
            </div>

            <div id="score-board">
                <div style="font-size: 0.7rem; color: #aaa;">PUNTAJE (META: 1000)</div>
                <div style="font-size: 1.4rem; font-weight: bold;">
                    <span id="score-text">0</span>
                    <span id="multiplier-badge" style="color:#ffcc00; font-size:1rem; margin-left:5px;">x1</span>
                </div>
            </div>
        </div>

        <div id="car-display-card">
            <img id="car-image" src="" alt="Carro">
            <div id="car-details">
                <h3 id="car-name" style="margin:0 0 5px 0; font-size: 1rem; color:#05d9e8;">Cargando PHP...</h3>
                <span id="car-status" style="font-size: 0.75rem; color: #aaa;">Sincronizando...</span>
                <span id="car-hint">Pista: Clic para analizar...</span>
            </div>
        </div>

        <div id="rating-controls">
            <button class="btn-rate" onclick="rateCarPHP('Malo')" style="border-color:#ff2a6d;">Horrible</button>
            <button class="btn-rate" onclick="rateCarPHP('Normal')" style="border-color:#ffea00;">Meh</button>
            <button class="btn-rate" onclick="rateCarPHP('Excelente')" style="border-color:#05d9e8;">Increíble</button>
        </div>
    </div>

    <div id="role-badge">ROL</div>

    <!-- HUD FPS & CIUDAD -->
    <div id="fps-hud">
        <div style="display:flex; flex-direction:column; align-items:center;">
            <div id="survival-timer">30s</div>
            <div id="city-hud">MODO CIUDAD GIGANTE<br><span style="font-size:1rem; color:#fff;">Kills: <span id="kill-count" style="color:#ffea00;">0</span>/10</span></div>
        </div>
    </div>

    <div id="cinematic-subtitle">VALENTÍN: "¡TE AMO!"</div>

    <!-- OVERLAY GAME OVER -->
    <div id="game-over">
        <h1 id="go-title">¡GAME OVER!</h1>
        <p id="go-desc" style="font-size: 1.1rem; color: #ccc;">Procesado vía servidor PHP.</p>
        <div class="btn-group-end">
            <button class="btn-end" style="background:#ff0000;" onclick="restartGamePHP()">Reiniciar</button>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>

    <script>
        let isMobile = false;
        let joyDir = { x: 0, y: 0 };
        let scene, camera, renderer;
        let selectedCharacter = null, currentCar = null, isCarInZone = false;
        let moveForward = false, moveBackward = false, moveLeft = false, moveRight = false, isDrifting = false;
        let yaw = 0, pitch = 0, isCityMode = false, isCinematicMode = false, isMurderMode = false, isEscapeMode = false;
        let playerCarGroup = null, carSpeedDrive = 0, carAngleDrive = 0, cityPedestrians = [], killsCount = 0;
        let survivalTime = 30, survivalInterval = null;

        function setDevice(device) {
            isMobile = (device === 'mobile');
            document.getElementById('device-select').style.display = 'none';
            document.getElementById('character-select').style.display = 'flex';
            if (isMobile) {
                document.getElementById('mobile-controls').style.display = 'flex';
                setupJoystick();
            }
        }

        function setupJoystick() {
            const joystick = document.getElementById('touch-joystick');
            const stick = document.getElementById('joystick-stick');
            joystick.addEventListener('touchmove', (e) => {
                let rect = joystick.getBoundingClientRect();
                const touch = e.touches[0];
                let x = touch.clientX - rect.left - 55;
                let y = touch.clientY - rect.top - 55;
                const dist = Math.hypot(x, y);
                if (dist > 35) { x = (x / dist) * 35; y = (y / dist) * 35; }
                stick.style.left = (x + 32) + 'px'; stick.style.top = (y + 32) + 'px';
                joyDir.x = x / 35; joyDir.y = y / 35;
                moveForward = joyDir.y < -0.3; moveBackward = joyDir.y > 0.3;
                moveLeft = joyDir.x < -0.3; moveRight = joyDir.x > 0.3;
            });
            joystick.addEventListener('touchend', () => {
                stick.style.top = '32px'; stick.style.left = '32px';
                moveForward = moveBackward = moveLeft = moveRight = false;
            });
        }

        function triggerMobileAction() {
            if (isCityMode) { isDrifting = true; setTimeout(() => isDrifting = false, 800); }
        }

        // COMUNICACIÓN CON SERVLET PHP
        async function fetchCarFromPHP() {
            const res = await fetch('backend.php?action=get_car');
            const data = await res.json();
            if (data.status === 'success') {
                spawnCarMesh(data.car);
            }
        }

        async function rateCarPHP(rating) {
            if (!isCarInZone) return;
            const res = await fetch('backend.php?action=rate_car', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ rating: rating })
            });
            const data = await res.json();

            document.getElementById('score-text').innerText = data.score;
            document.getElementById('fury-bar').style.width = data.fury + '%';
            document.getElementById('fury-percentage').innerText = data.fury + '%';
            document.getElementById('multiplier-badge').innerText = 'x' + data.multiplier;

            if (data.triggerVictory) { triggerHappyEndingCity(); return; }
            if (data.triggerTerror) { startNightTerrorMode(); return; }

            isCarInZone = false;
        }

        async function fetchMurderRolePHP() {
            const res = await fetch('backend.php?action=get_murder_role');
            const data = await res.json();
            const badge = document.getElementById('role-badge');
            badge.innerText = "ROL PHP: " + data.role; badge.style.display = 'block';
            
            data.npcs.forEach(name => {
                const npc = createHumanoid(1.8, Math.random() * 0xffffff, 0xffdbac, false, false, name);
                npc.position.set((Math.random() - 0.5) * 16, 0.4, (Math.random() - 0.5) * 16);
                scene.add(npc);
            });
        }

        async function restartGamePHP() {
            await fetch('backend.php?action=reset_session');
            location.reload();
        }

        function init3D() {
            scene = new THREE.Scene();
            scene.background = new THREE.Color(0x020205);
            camera = new THREE.PerspectiveCamera(45, window.innerWidth / window.innerHeight, 0.1, 1000);
            camera.position.set(0, 4.5, 14); camera.lookAt(0, 1.4, 0);

            renderer = new THREE.WebGLRenderer({ antialias: true });
            renderer.setSize(window.innerWidth, window.innerHeight);
            document.body.appendChild(renderer.domElement);

            const ambient = new THREE.AmbientLight(0xffffff, 0.4); scene.add(ambient);
            const road = new THREE.Mesh(new THREE.PlaneGeometry(12, 100), new THREE.MeshStandardMaterial({ color: 0x0a0a0f }));
            road.rotation.x = -Math.PI / 2; road.position.z = -20; scene.add(road);
        }

        function createHumanoid(height, color, skinColor = 0xffdbac, isValentin = false, isRubio = false, nameText = "") {
            const group = new THREE.Group();
            const torso = new THREE.Mesh(new THREE.CylinderGeometry(0.3, 0.25, 0.9, 8), new THREE.MeshStandardMaterial({ color: color }));
            torso.position.y = (height / 2); group.add(torso);

            const head = new THREE.Mesh(new THREE.BoxGeometry(0.4, 0.4, 0.4), new THREE.MeshStandardMaterial({ color: skinColor }));
            head.position.y = torso.position.y + 0.5; group.add(head);

            if (nameText !== "") {
                const canvas = document.createElement('canvas'); canvas.width = 128; canvas.height = 32;
                const ctx = canvas.getContext('2d');
                ctx.fillStyle = 'rgba(0,0,0,0.6)'; ctx.fillRect(0,0,128,32);
                ctx.fillStyle = '#ffffff'; ctx.font = '16px sans-serif'; ctx.textAlign = 'center'; ctx.fillText(nameText, 64, 22);
                const label = new THREE.Mesh(new THREE.PlaneGeometry(1.2, 0.3), new THREE.MeshBasicMaterial({ map: new THREE.CanvasTexture(canvas), transparent: true }));
                label.position.y = head.position.y + 0.5; group.add(label);
            }
            return group;
        }

        function spawnCarMesh(carData) {
            if (currentCar) scene.remove(currentCar);
            const carGroup = new THREE.Group();
            const body = new THREE.Mesh(new THREE.BoxGeometry(2.3, 0.8, 4.6), new THREE.MeshStandardMaterial({ color: parseInt(carData.color) }));
            body.position.y = 0.6; carGroup.add(body);
            carGroup.position.set(0, 0, -35); scene.add(carGroup);
            currentCar = carGroup; isCarInZone = true;

            document.getElementById('car-name').innerText = carData.name;
            document.getElementById('car-image').src = carData.img;
            document.getElementById('car-hint').innerText = carData.hint;
        }

        function triggerHappyEndingCity() {
            isCityMode = true;
            document.getElementById('ui-layer').style.display = 'none';
            document.getElementById('fps-hud').style.display = 'flex';
            document.getElementById('city-hud').style.display = 'block';

            while(scene.children.length > 0){ scene.remove(scene.children[0]); }
            const cityRoad = new THREE.Mesh(new THREE.PlaneGeometry(300, 300), new THREE.MeshStandardMaterial({ color: 0x111118 }));
            cityRoad.rotation.x = -Math.PI / 2; scene.add(cityRoad);

            playerCarGroup = new THREE.Group();
            const carBody = new THREE.Mesh(new THREE.BoxGeometry(2.6, 1.0, 5.0), new THREE.MeshStandardMaterial({ color: 0x0055ff }));
            carBody.position.y = 0.5; playerCarGroup.add(carBody);
            scene.add(playerCarGroup);

            cityPedestrians = [];
            for (let i = 0; i < 30; i++) {
                const p = createHumanoid(1.8, 0xffea00);
                p.position.set((Math.random() - 0.5) * 180, 0, (Math.random() - 0.5) * 180);
                scene.add(p); cityPedestrians.push(p);
            }
            setupControls();
        }

        function startNightTerrorMode() {
            isEscapeMode = true;
            document.getElementById('ui-layer').style.display = 'none';
            document.getElementById('fps-hud').style.display = 'flex';
            while(scene.children.length > 0){ scene.remove(scene.children[0]); }

            survivalInterval = setInterval(() => {
                survivalTime--;
                document.getElementById('survival-timer').innerText = survivalTime + 's';
                if (survivalTime <= 0) { clearInterval(survivalInterval); triggerHappyEndingCity(); }
            }, 1000);
            setupControls();
        }

        function startMurderMysteryMode() {
            isMurderMode = true;
            document.getElementById('ui-layer').style.display = 'none';
            document.getElementById('fps-hud').style.display = 'flex';
            while(scene.children.length > 0){ scene.remove(scene.children[0]); }
            fetchMurderRolePHP();
            setupControls();
        }

        function setupControls() {
            document.addEventListener('keydown', (e) => {
                if (e.key === 'w' || e.key === 'W') moveForward = true;
                if (e.key === 's' || e.key === 'S') moveBackward = true;
                if (e.key === 'a' || e.key === 'A') moveLeft = true;
                if (e.key === 'd' || e.key === 'D') moveRight = true;
                if (e.code === 'Space') isDrifting = true;
            });
            document.addEventListener('keyup', (e) => {
                if (e.key === 'w' || e.key === 'W') moveForward = false;
                if (e.key === 's' || e.key === 'S') moveBackward = false;
                if (e.key === 'a' || e.key === 'A') moveLeft = false;
                if (e.key === 'd' || e.key === 'D') moveRight = false;
                if (e.code === 'Space') isDrifting = false;
            });
        }

        function animate() {
            requestAnimationFrame(animate);
            if (currentCar && !isCityMode && !isMurderMode && !isEscapeMode) {
                if (isCarInZone) {
                    if (currentCar.position.z < 0) currentCar.position.z += 0.18;
                } else {
                    currentCar.position.z += 0.4;
                    if (currentCar.position.z > 30) fetchCarFromPHP();
                }
            } else if (isCityMode && playerCarGroup) {
                if (moveForward) carSpeedDrive = Math.min(carSpeedDrive + 0.015, 0.5);
                else carSpeedDrive *= 0.95;

                const turn = isDrifting ? 0.09 : 0.04;
                if (moveLeft) carAngleDrive += turn;
                if (moveRight) carAngleDrive -= turn;

                playerCarGroup.rotation.y = carAngleDrive;
                playerCarGroup.translateZ(-carSpeedDrive);
                camera.position.set(playerCarGroup.position.x, 22, playerCarGroup.position.z + 14);
                camera.lookAt(playerCarGroup.position);
            }
            renderer.render(scene, camera);
        }

        function startGame(character, mode) {
            selectedCharacter = character;
            document.getElementById('character-select').style.display = 'none';
            init3D();
            if (mode === 'murder') startMurderMysteryMode();
            else {
                document.getElementById('ui-layer').style.display = 'flex';
                fetchCarFromPHP();
            }
            animate();
        }
    </script>
</body>
</html>
