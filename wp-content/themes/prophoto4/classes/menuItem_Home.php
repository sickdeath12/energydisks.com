<?php


class ppMenuItem_Home extends ppMenuItem_Internal {


	public function url() {
		return pp::site()->url;
	}

	public function aTag() {
		$this->rel = 'home';
		$this->titleAttr = pp::site()->name;
		return parent::aTag();
	}
}

