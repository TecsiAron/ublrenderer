UBLRenderer
===========

STILL IN TESTING PHASE  
Should not be used in production  
PHP Library to convert RO eFactura UBL/ZIP files to HTML/PDF*  
Requirments:  
- php 8.2 or newer  
- ext-xmlreader  
- ext-bcmath  
- ext-zip  
- ext-libxml
  
Instalation:  
```
composer require tecsiaron/ublrenderer
```
Usage in shell:
```
php vendor/bin/ubl2html.php <input.xml or input.zip> <output.html>
```
Simple usage in php (with XML file):
```PHP
$content=file_get_contents("path_to.xml");
$renderer = new UBLRenderer($content);
$renderer->WriteFile();
```
Simple usage with ANAF ZIP
```PHP
$content=UBLRenderer::LoadUBLFromZip("test.zip")->ubl;
$renderer = new UBLRenderer($content);
$renderer->WriteFile();
```
In both of th e aboe cases the file will be written to vendor/tecsiaron/ublrenderer/output  
To specify where files are written call WriteFile (or WriteFiles) like this:  
```PHP
$renderer->WriteFiles(new HTMLFileWriter("path_to_output.html"));
```
Advanced usage:
```PHP
// get the contents of the XML
$renderer = new UBLRenderer($content);
$invoice=$renderer->ParseUBL();
$validation=$invoice->CanRender();
$validationFailReason="Validation failed:\n";
if(is_array($validation))
{
	echo "Failed to render invoice: ". implode("\n", $validation)
}
else
{
	$html=$renderer->CreateHTML(invoice);
}
```
For PDF support install ublrenderer-pdf and use PDFWriter
```PHP
composer require tecsiaron/ublrenderer-pdf
```