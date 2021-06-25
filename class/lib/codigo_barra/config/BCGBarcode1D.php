<?php
function baseCustomSetup($barcode, $get) {
	$path='class'.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'codigo_barra'.DIRECTORY_SEPARATOR;

    $font_dir = $path . 'font';

    if (isset($get['thickness'])) {
        $barcode->setThickness(max(9, min(90, intval($get['thickness']))));
    }
    $font = 0;
    if ($get['font_family'] !== '0' && intval($get['font_size']) >= 1) {
	
        $font = new BCGFontFile($font_dir . '/' . $get['font_family'], intval($get['font_size']));
    }

    $barcode->setFont($font);
}
?>