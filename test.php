<?php

$directoryValid = __DIR__ . DIRECTORY_SEPARATOR . 'examples_valid';
$directoryInvalid = __DIR__ . DIRECTORY_SEPARATOR . 'examples_invalid';
$xsdPath = __DIR__ . DIRECTORY_SEPARATOR . 'svg.xsd';

$result = init($directoryValid, $directoryInvalid, $xsdPath);
if (count($result) === 0) {
    echo 'All examples correct';
} else {
    foreach ($result as $error) {
        echo $error . "\r\n";
    }
}

/**
 * @param string $directoryValid
 * @param string $directoryInvalid
 * @param string $xsdPath
 * @return array
 */
function init($directoryValid, $directoryInvalid, $xsdPath)
{
    $errors = [];

    $files = getFileList($directoryValid);
    foreach ($files as $file) {
        $result = testFile($file, $xsdPath);
        if ($result !== true) {
            $errors[] = $file . ' is invalid';
        }
    }

    $files = getFileList($directoryInvalid);
    foreach ($files as $file) {
        $result = testFile($file, $xsdPath);
        if ($result === true) {
            $errors[] = $file . ' is valid. But don\'t';
        }
    }

    return $errors;
}

/**
 * @param string $dir
 * @return array
 */
function getFileList($dir)
{
    $resFiles = [];
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            $path = $dir.'/'.$file;
            if (is_file($path)) {
                $resFiles[] = $path;
            }
            if (is_dir($path)) {
                $addFiles = getFileList($path);
                $resFiles = array_merge($resFiles, $addFiles);
            }
        }
    }

    return $resFiles;
}

/**
 * @param string $xmlPath
 * @param string $xsdPath
 * @return array|bool
 */
function testFile($xmlPath, $xsdPath)
{
    libxml_use_internal_errors(true);

    $xml = new DOMDocument();
    $xml->load($xmlPath);
    $result = $xml->schemaValidate($xsdPath);

    if (!$result) {
        $errors = libxml_get_errors();
        libxml_clear_errors();

        return $errors;
    }

    return true;
}
