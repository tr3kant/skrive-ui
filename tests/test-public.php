<?php

class SUI_Public_Test extends WP_UnitTestCase {

	function test_sample() {
		// replace this with some actual testing code
		$this->assertTrue( true );
	}

	function test_class_exists() {
		$this->assertTrue( class_exists( 'Skrive_UI_Public') );
	}

	function test_class_access() {
		$this->assertTrue( skrive_ui()->public instanceof Skrive_UI_Public );
	}
}
