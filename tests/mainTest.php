<?php
/**
 * test for tomk79\px2-path-resolver
 */

class mainTest extends PHPUnit_Framework_TestCase{
	private $fs;

	public function setup(){
		mb_internal_encoding('UTF-8');
		$this->fs = new tomk79\filesystem();
	}


	/**
	 * 普通に実行してみるテスト
	 */
	public function testStandard(){
		$output = $this->passthru( [
			'php',
			__DIR__.'/../htdocs/.px_execute.php',
			'/'
		] );
		// var_dump($output);
		// $this->assertEquals( 1, preg_match('/'.preg_quote('data-dec=', '/').'/is', $output) );

	}

	/**
	 * パブリッシュ実行
	 */
	public function testAsPublish(){

		$output = $this->passthru( [
			'php',
			__DIR__.'/../htdocs/.px_execute.php' ,
			'/?PX=publish.run' ,
		] );
		// var_dump($output);

		// 後始末
		$output = $this->passthru( [
			'php', __DIR__.'/../htdocs/.px_execute.php', '/?PX=clearcache'
		] );

		clearstatcache();
		$this->assertTrue( !is_dir( __DIR__.'/../htdocs/caches/p/' ) );

	}






	/**
	 * コマンドを実行し、標準出力値を返す
	 * @param array $ary_command コマンドのパラメータを要素として持つ配列
	 * @return string コマンドの標準出力値
	 */
	private function passthru( $ary_command ){
		$cmd = array();
		foreach( $ary_command as $row ){
			$param = '"'.addslashes($row).'"';
			array_push( $cmd, $param );
		}
		$cmd = implode( ' ', $cmd );
		ob_start();
		passthru( $cmd );
		$bin = ob_get_clean();
		return $bin;
	}

}
