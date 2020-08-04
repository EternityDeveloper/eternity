<?php ob_start();
define('IN_CB', true);
$path='class'.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'codigo_barra'.DIRECTORY_SEPARATOR;

 
include_once($path.'include/function.php');

function showError() {
    header('Content-Type: image/png');
    readfile('error.png');
    exit;
}



$_CONFIG=array(
	"filetype"=>"PNG",
	"dpi"=>72,
	"scale"=>1,
	"rotation"=>0,
	"font_family"=>0,
	"font_size"=>0,
	"thickness"=>30,
	"checksum"=>"",
	"code"=>"BCGean13",
	"text"=>sprintf("%013d",$_GET['code'])
);

$requiredKeys = array('code', 'filetype', 'dpi', 'scale', 'rotation', 'font_family', 'font_size', 'text');

// Check if everything is present in the request
foreach ($requiredKeys as $key) {
    if (!isset($_CONFIG[$key])) {
        showError();
    }
}


if (!preg_match('/^[A-Za-z0-9]+$/', $_CONFIG['code'])) {
    showError();
}

$code = $_CONFIG['code'];


// Check if the code is valid
if (!file_exists($path.'config' . DIRECTORY_SEPARATOR . $code . '.php')) {
    showError();
}
include_once($path .'config' . DIRECTORY_SEPARATOR. $code . '.php');

$class_dir =$path;

require_once($class_dir . DIRECTORY_SEPARATOR . 'BCGColor.php');
require_once($class_dir . DIRECTORY_SEPARATOR . 'BCGBarcode.php');
require_once($class_dir . DIRECTORY_SEPARATOR . 'BCGDrawing.php');
require_once($class_dir . DIRECTORY_SEPARATOR . 'BCGFontFile.php');

if (!include_once($class_dir . $classFile)) {
    showError();
}

include_once($path.'config' . DIRECTORY_SEPARATOR . $baseClassFile);

$filetypes = array('PNG' => BCGDrawing::IMG_FORMAT_PNG, 'JPEG' => BCGDrawing::IMG_FORMAT_JPEG, 'GIF' => BCGDrawing::IMG_FORMAT_GIF);

$drawException = null;
try {
    $color_black = new BCGColor(0, 0, 0);
    $color_white = new BCGColor(255, 255, 255);
	
    $code_generated = new $className();

    if (function_exists('baseCustomSetup')) {
        baseCustomSetup($code_generated, $_CONFIG);
    }

    if (function_exists('customSetup')) {
        customSetup($code_generated, $_CONFIG);
    }

    $code_generated->setScale(max(1, min(4, $_CONFIG['scale'])));
    $code_generated->setBackgroundColor($color_white);
    $code_generated->setForegroundColor($color_black);

    if ($_CONFIG['text'] !== '') {
        $text = convertText($_CONFIG['text']);
        $code_generated->parse($text);
    }
} catch(Exception $exception) {
    $drawException = $exception;
}

$drawing = new BCGDrawing('', $color_white);
if($drawException) {
    $drawing->drawException($drawException);
} else {
    $drawing->setBarcode($code_generated);
    $drawing->setRotationAngle($_CONFIG['rotation']);
    $drawing->setDPI($_CONFIG['dpi'] === 'NULL' ? null : max(72, min(300, intval($_CONFIG['dpi']))));
    $drawing->draw();
}


$drawing->setFilename("recibos/".$_CONFIG['text'].".png");
$drawing->finish($filetypes[$_CONFIG['filetype']]);
echo "recibos/".$_CONFIG['text'].".png";
?>