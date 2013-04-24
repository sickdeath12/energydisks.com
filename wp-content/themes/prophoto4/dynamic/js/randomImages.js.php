<?php 



if ( ppBio::randomizePics() ) {
	
	// build the array of available bio pics
	$c = 0;
	$biopics_array = '';
	for ( $i = 1; $i <= pp::num()->maxBioImages; $i++ ) {
		if ( ppImg::id( 'biopic' . $i )->exists ) {
			$biopics_array .= "biopics[$c]='" . ppImg::id( 'biopic' . $i )->url . "';\n\t";
			$c++;
		}
	}
	$biopic_html = ppImg::id( 'biopic1' )->htmlAttr;
	$p4_site_name = pp::site()->name;
	
	$jsCode .= <<<JAVASCRIPT
	function ppRandomizeBiopic() {
		var biopics = new Array();
		$biopics_array
		var biopic = Math.floor(Math.random()*(biopics.length));
		var src = biopics[biopic];
		document.write('<img id="biopic" src="'+biopics[biopic]+'" $biopic_html alt="$p4_site_name bio picture" class="bio-col" />');
	}
	
JAVASCRIPT;
	
}


 




