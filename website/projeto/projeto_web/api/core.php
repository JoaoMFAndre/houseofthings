<?php

function connectDB($db){
    try {
        $pdo = new PDO(
            'mysql:host=' . $db['host'] . ';' .
                'dbname=' . $db['dbname'] . ';' .
                'port=' . $db['port'] . ';' .
                'charset=' . $db['charset'] . ';',
            $db['username'],
            $db['password']
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die('Erro ao ligar ao servidor ' . $e->getMessage());
    }
    return $pdo;
}
/**
 * Verifica se o modo DEBUG está definido e ativo
 * @return boolean
 */
function debug()
{
    if (defined('DEBUG') && DEBUG) {
        return true;
    }
    return false;
}

function slugify($text = '')
{
    if ($text != '') {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);
        // trim
        $text = trim($text, '-');
        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);
        // lowercase
        $text = strtolower($text);
        return $text;
    }
    return FALSE;
}
