<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>API Gym - Panel</title>
	<style>
		body { font-family: Arial, sans-serif; margin: 2em; }
		form { margin-bottom: 2em; padding: 1em; border: 1px solid #ccc; border-radius: 8px; max-width: 400px; }
		label { display: block; margin-top: 1em; }
		input, select { width: 100%; padding: 0.5em; }
		button { margin-top: 1em; padding: 0.5em 1em; }
		.success { color: green; }
		.error { color: red; }
	</style>
</head>
<body>
	<h1>Panel de administración API Gym</h1>

	<h2>Crear Ejercicio</h2>
	<form id="form-ejercicio">
		<label>Nombre:<input type="text" name="nombre" required></label>
		<label>Nivel:<input type="text" name="nivel" required></label>
		<label>Músculo:<input type="text" name="musculo" required></label>
		<label>Imagen (URL):<input type="text" name="imagen" required></label>
		<label>Video (URL):<input type="text" name="video" required></label>
		<button type="submit">Crear Ejercicio</button>
		<div id="msg-ejercicio"></div>
		</form>

		<h2>Asignar Ejercicio a Plan</h2>
		<form id="form-ejercicio-plan">
			<label>Ejercicio:
				<select name="id_ejercicio" id="select-ejercicio" required>
					<option value="">Cargando ejercicios...</option>
				</select>
			</label>
			<label>Plan:
				<select name="id_plan" id="select-plan" required>
					<option value="">Cargando planes...</option>
				</select>
			</label>
			<button type="submit">Asignar Ejercicio a Plan</button>
			<div id="msg-ejercicio-plan"></div>
		</form>

	<h2>Crear Plan</h2>
	<form id="form-plan">
		<label>Nombre del plan:<input type="text" name="nombre" required></label>
		<button type="submit">Crear Plan</button>
		<div id="msg-plan"></div>
	</form>

	<script>
	// Cambia esta API key por la tuya real
	const API_KEY = '1234';

	document.getElementById('form-ejercicio').onsubmit = async function(e) {
		e.preventDefault();
		const data = Object.fromEntries(new FormData(this));
		const res = await fetch('api/ejercicios.php', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'Authorization': API_KEY
			},
			body: JSON.stringify(data)
		});
		const msg = document.getElementById('msg-ejercicio');
		if (res.ok) {
			msg.textContent = 'Ejercicio creado correctamente';
			msg.className = 'success';
			this.reset();
		} else {
			const error = await res.json();
			msg.textContent = error.error || 'Error al crear ejercicio';
			msg.className = 'error';
		}
	};

	document.getElementById('form-plan').onsubmit = async function(e) {
		e.preventDefault();
		const data = Object.fromEntries(new FormData(this));
		const res = await fetch('api/planes.php', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'Authorization': API_KEY
			},
			body: JSON.stringify(data)
		});
		const msg = document.getElementById('msg-plan');
		if (res.ok) {
			msg.textContent = 'Plan creado correctamente';
			msg.className = 'success';
			this.reset();
		} else {
			const error = await res.json();
			msg.textContent = error.error || 'Error al crear plan';
			msg.className = 'error';
		}
	};
	// Asignar ejercicio a plan
	document.getElementById('form-ejercicio-plan').onsubmit = async function(e) {
		e.preventDefault();
		const data = Object.fromEntries(new FormData(this));
		const res = await fetch('api/ejercicio-plan.php', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'Authorization': API_KEY
			},
			body: JSON.stringify(data)
		});
		const msg = document.getElementById('msg-ejercicio-plan');
		if (res.ok) {
			msg.textContent = 'Ejercicio asignado al plan correctamente';
			msg.className = 'success';
			this.reset();
		} else {
			const error = await res.json();
			msg.textContent = error.error || 'Error al asignar ejercicio al plan';
			msg.className = 'error';
		}
	};
	// Cargar ejercicios y planes en los selects
	async function cargarSelects() {
		const headers = { 'Authorization': API_KEY };
		// Cargar ejercicios
		const resEj = await fetch('api/ejercicios.php', { headers });
		const ejercicios = resEj.ok ? await resEj.json() : [];
		const selectEj = document.getElementById('select-ejercicio');
		selectEj.innerHTML = ejercicios.length
			? ejercicios.map(e => `<option value="${e.id}">${e.nombre} (${e.musculo})</option>`).join('')
			: '<option value="">No hay ejercicios</option>';
		// Cargar planes
		const resPl = await fetch('api/planes.php', { headers });
		const planes = resPl.ok ? await resPl.json() : [];
		const selectPl = document.getElementById('select-plan');
		selectPl.innerHTML = planes.length
			? planes.map(p => `<option value="${p.id}">${p.nombre}</option>`).join('')
			: '<option value="">No hay planes</option>';
	}

	window.addEventListener('DOMContentLoaded', cargarSelects);
	</script>
</body>
</html>
