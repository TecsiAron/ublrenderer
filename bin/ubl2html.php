<?php
/*
 *  Copyright [2024] [Tecsi Aron]
 *
 *     Licensed under the Apache License, Version 2.0 (the "License");
 *     you may not use this file except in compliance with the License.
 *     You may obtain a copy of the License at
 *
 *       http://www.apache.org/licenses/LICENSE-2.0
 *
 *     Unless required by applicable law or agreed to in writing, software
 *     distributed under the License is distributed on an "AS IS" BASIS,
 *     WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *     See the License for the specific language governing permissions and
 *     limitations under the License.
 */

use EdituraEDU\UBLRenderer\HTMLFileWriter;
use EdituraEDU\UBLRenderer\UBLRenderer;

include $_composer_autoload_path ?? __DIR__ . '/../vendor/autoload.php';
$showHelp = false;
if ($argv[1] == "help" || sizeof($argv) != 3)
{
    $showHelp = true;
}
else
{
    $inputFile = $argv[1];
    $outputFile = $argv[2];
    if (!file_exists($inputFile))
    {
        echo "Input file does not exist!\n";
        exit(1);
    }
    if (file_exists($outputFile))
    {
        echo "Output file already exists!\n";
        exit(1);
    }
    if (substr($inputFile, -4) == ".zip")
    {
        $xml = UBLRenderer::LoadUBLFromZip($inputFile)->ubl;
    }
    else
    {
        $xml = file_get_contents($inputFile);
    }
    $renderer = new UBLRenderer($xml);
    try
    {
        $renderer->WriteFiles([new HTMLFileWriter($outputFile)]);
        echo "Written to $outputFile".PHP_EOL;
    }
    catch (Exception $e)
    {
        echo $e->getMessage() . "\n" . $e->getTraceAsString();
        exit(1);
    }
}
if ($showHelp)
{
    echo "Usage: php ubl2html.php <input.xml or input.zip> <output.html>\n";
    exit(0);
}
