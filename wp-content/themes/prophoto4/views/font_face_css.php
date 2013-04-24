@font-face {
    font-family: '<?php echo $font->name ?>';
    src: url('<?php echo $font->urlStart . $font->slug ?>-webfont.eot');
    src: url('<?php echo $font->urlStart . $font->slug ?>-webfont.eot?#iefix') format('embedded-opentype'),
         url('<?php echo $font->urlStart . $font->slug ?>-webfont.woff') format('woff'),
         url('<?php echo $font->urlStart . $font->slug ?>-webfont.ttf') format('truetype'),
         url('<?php echo $font->urlStart . $font->slug ?>-webfont.svg#<?php echo $font->name ?>') format('svg');
    font-weight: normal;
    font-style: normal;
}
