<?php

/**
 * Envoie un code HTTP + inclut la page correspondante + stoppe le script
 *
 * @param int    $code       Code HTTP (ex: 404, 403, 500…)
 * @param string|null $file  Fichier à inclure (optionnel). Si null => /errors/{code}.php
 */
function sendHttpError(int $code, ?string $file = null): void
{
    // Définit le code HTTP
    http_response_code($code);

    // Si aucun fichier fourni, on prend par défaut /errors/{code}.php
    if ($file === null) {
        $file = __DIR__.'/../../' . $code . '.php';
    }

    // Si le fichier existe on l'inclut, sinon on affiche un message simple
    if (is_file($file)) {
        require $file;
    } else {
        echo "<h1>Erreur {$code}</h1>";
    }

    // On termine proprement
    exit;
}