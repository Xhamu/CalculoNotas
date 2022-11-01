<?php

declare(strict_types=1);

if (isset($_POST['enviar'])) {
    $data['errores'] = checkForm($_POST);
    $data['input'] = filter_var_array($_POST, FILTER_SANITIZE_SPECIAL_CHARS);
    if (count($data['errores']) == 0) {
        
    }
}

function checkForm(array $datos): array {
    $errores = [];
    if (empty($datos['json_notas'])) {
        $errores['json_notas'] = "Este campo es obligatorio";
    } else {
        // validar que es formato json dado
    }
}

include 'views/templates/header.php';
include 'views/calculoNotas.SamuelRodriguez.view.php';
include 'views/templates/footer.php';
