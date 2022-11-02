<?php

declare(strict_types=1);

if (isset($_POST['enviar'])) {
    $data['errores'] = checkForm($_POST);
    $data['input'] = filter_var_array($_POST, FILTER_SANITIZE_SPECIAL_CHARS);
    if (count($data['errores']) == 0) {
        $jsonArray = json_decode($_POST['json_notas'],true);
        $resultado = datosAsignaturas($jsonArray);
        $data['resultado'] = $resultado;
    }
}

function datosAsignaturas(array $materias): array {
    $resultado = [];
    $arrayAlumnos = [];
    foreach ($materias as $nombreMateria => $alumnos) {
        $resultado[$nombreMateria] = [];
        $aprobados = 0;
        $suspensos = 0;
        $max = [
            'alumno' => '',
            'nota' => -1
        ];

        $min = [
            'alumno' => '',
            'nota' => 11
        ];
        $totalNotas = 0;
        $notaAcumulada = 0;
        $contarAlumnos = 0;
        foreach ($alumnos as $alumno => $notas) {
            $notaAcumuladaAlumno = 0;
            if (!isset($arrayAlumnos[$alumno])) {
                $arrayAlumnos[$alumno] = ['aprobados' => 0, 'suspensos' => 0];
            }
            $contarAlumnos++;
            if (max($notas) > $max['nota']) {
                $max['alumno'] = $alumno;
                $max['nota'] = max($notas);
            }
            if (min($notas) < $min['nota']) {
                $min['alumno'] = $alumno;
                $min['nota'] = min($notas);
            }
            foreach ($notas as $nota) {
                $notaAcumulada += $nota;
                $notaAcumuladaAlumno += $nota;
                $totalNotas++;
            }
            if (($notaAcumuladaAlumno / count($notas)) < 5) {
                $suspensos++;
                $arrayAlumnos[$alumno]['suspensos']++;
            } else {
                $aprobados++;
                $arrayAlumnos[$alumno]['aprobados']++;
            }
        }
        if ($contarAlumnos > 0) {
            $resultado[$nombreMateria]['media'] = $notaAcumulada / $totalNotas;
            $resultado[$nombreMateria]['max'] = $max;
            $resultado[$nombreMateria]['min'] = $min;
        } else {
            $resultado[$nombreMateria]['media'] = 0;
        }
        $resultado[$nombreMateria]['aprobados'] = $aprobados;
        $resultado[$nombreMateria]['suspensos'] = $suspensos;
    }

    return array('modulos' => $resultado, 'alumnos' => $arrayAlumnos);
}

function checkForm(array $datos): array {
    $errores = [];
    if (empty($datos['json_notas'])) {
        $errores['json_notas'] = "Este campo es obligatorio";
    } else {
        $modulos = json_decode($datos['json_notas'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $errores['json_notas'] = "El formato no es el correcto";
        } else {
            $erroresJson = "";
            foreach ($modulos as $modulo => $alumnos) {
                if (empty($modulo)) {
                    $erroresJson .= "El nombre del módulo no puede estar vacio";
                }
                if (!is_array($alumnos)) {
                    $erroresJson .= "El modulo '" . htmlentities($modulo) . " no tiene un array alumnos<br>";
                } else {
                    foreach ($alumnos as $alumno => $notas) {
                        if (empty($alumno)) {
                            $erroresJson .= "El modulo " . htmlentities($modulo) . " tiene un alumno sin nombre<br>";
                        }
                        if (!is_array($notas)) {
                            $erroresJson .= "En " . htmlentities($modulo) . " el alumno " . htmlentities($alumno) . " no tiene un array de notas<br>";
                        } else {
                            foreach ($notas as $nota) {
                                if (!is_numeric($nota)) {
                                    $erroresJson .= "En " . htmlentities($modulo) . " el alumno " . htmlentities($alumno) . " tiene una nota inválida<br>";
                                } else {
                                    if ($nota < 0 || $nota > 10) {
                                        $erroresJson .= "En " . htmlentities($modulo) . " el alumno " . htmlentities($alumno) . " tiene una nota de " . $nota . "<br>";
                                    }
                                }
                            }
                        }
                    }
                }
            }
            if (!empty($erroresJson)) {
                $errores['json_notas'] = $erroresJson;
            }
        }
    }
    return $errores;
}

include 'views/templates/header.php';
include 'views/calculoNotas.SamuelRodriguez.view.php';
include 'views/templates/footer.php';
