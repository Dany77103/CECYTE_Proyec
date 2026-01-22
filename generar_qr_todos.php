<?php
// generar_qr_todos.php
// Script temporal para generar QR de todos los alumnos

// Incluir tu archivo principal que tiene las funciones QR
include 'qr_asistencia.php'; // Cambia el nombre si tu archivo principal se llama diferente

// Forzar la acción a "generar_todos_qr"
$_REQUEST['action'] = 'generar_todos_qr';

// Llamar a la función directamente
generarTodosQR();

// Fin del script
