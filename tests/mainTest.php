<?php
/**
 * test for tomk79\px2-path-resolver
 */

class mainTest extends PHPUnit_Framework_TestCase{
	private $fs;
	private $testJson;

	public function setup(){
		mb_internal_encoding('UTF-8');
		$this->fs = new tomk79\filesystem();

		// テスト用設定
		$this->testJson = array();
		$this->testJson['relate'] = '{"to": "relate","supply_index_filename": null}';
		$this->testJson['absolute'] = '{"to": "absolute","supply_index_filename": null}';
		$this->testJson['pass'] = '{"to": "pass","supply_index_filename": null}';
		$this->testJson['relate_supply'] = '{"to": "relate","supply_index_filename": true}';
		$this->testJson['absolute_supply'] = '{"to": "absolute","supply_index_filename": true}';
		$this->testJson['pass_supply'] = '{"to": "pass","supply_index_filename": true}';
		$this->testJson['relate_strip'] = '{"to": "relate","supply_index_filename": false}';
		$this->testJson['absolute_strip'] = '{"to": "absolute","supply_index_filename": false}';
		$this->testJson['pass_strip'] = '{"to": "pass","supply_index_filename": false}';
	}


	/**
	 * 相対パスに変換するテスト
	 */
	public function testRelate(){
		$this->fs->save_file( __DIR__.'/testdata/standard/px-files/options.json', $this->testJson['relate'] );
		$output = $this->passthru( ['php', __DIR__.'/testdata/standard/.px_execute.php', '/'] );
		// var_dump($output);

		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="./abc/def/ghi.html">test1</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="./abc/def/ghi.html">test2</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<link href="./common/styles/contents.css" />', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<img src="./common/images/title.gif" alt="img1" />', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<script src="./common/scripts/contents.js"></script>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="./">test3-1</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="./#test">test3-2</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="./?test=abc">test3-3</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="./?test=abc#test">test3-4</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="./index.html?test=abc#test">test3-5</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="#test4">test4</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "./common/styles/contents1.css";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "./common/styles/contents2.css";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "./common/styles/contents3.css" all and (max-width:580px);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "./common/styles/contents4.css" all and (max-width:580px);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "./common/styles/contents1.css?time=1234567890";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "./common/styles/contents2.css?time=1234567890";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "./common/styles/contents3.css?time=1234567890" all and (max-width:580px);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "./common/styles/contents4.css?time=1234567890" all and (max-width:580px);', '/').'/s', $output) );

		$this->assertEquals( 1, preg_match('/'.preg_quote('background-image: url("./common/images/title1.gif");', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('background-image: url("./common/images/title2.gif");', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('background-image: url("./common/images/title3.gif");', '/').'/s', $output) );

		$this->assertEquals( 1, preg_match('/'.preg_quote('border-image: url(&quot;./common/images/title1.gif&quot;);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('border-image: url(&quot;./common/images/title2.gif&quot;);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('border-image: url(&quot;./common/images/title3.gif&quot;);', '/').'/s', $output) );

		$this->fs->save_file( __DIR__.'/testdata/standard/px-files/options.json', $this->testJson['relate'] );
		$output = $this->passthru( ['php', __DIR__.'/testdata/standard/.px_execute.php', '/path_test_1/'] );
		// var_dump($output);

		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="../abc/def/ghi.html">test1</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="../abc/def/ghi.html">test2</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<link href="../common/styles/contents.css" />', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<img src="../common/images/title.gif" alt="img1" />', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<script src="../common/scripts/contents.js"></script>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="./">test3-1</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="./#test">test3-2</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="./?test=abc">test3-3</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="./?test=abc#test">test3-4</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="./index.html?test=abc#test">test3-5</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="#test4">test4</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "../common/styles/contents1.css";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "../common/styles/contents2.css";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "../common/styles/contents3.css" all and (max-width:580px);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "../common/styles/contents4.css" all and (max-width:580px);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "../common/styles/contents1.css?time=1234567890";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "../common/styles/contents2.css?time=1234567890";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "../common/styles/contents3.css?time=1234567890" all and (max-width:580px);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "../common/styles/contents4.css?time=1234567890" all and (max-width:580px);', '/').'/s', $output) );

		$this->assertEquals( 1, preg_match('/'.preg_quote('background-image: url("../common/images/title1.gif");', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('background-image: url("../common/images/title2.gif");', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('background-image: url("../common/images/title3.gif");', '/').'/s', $output) );

		$this->assertEquals( 1, preg_match('/'.preg_quote('border-image: url(&quot;../common/images/title1.gif&quot;);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('border-image: url(&quot;../common/images/title2.gif&quot;);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('border-image: url(&quot;../common/images/title3.gif&quot;);', '/').'/s', $output) );

		// 属性値中の改行を削除してしまわないことを確認するテスト。
		$this->assertEquals( 1, preg_match('/'.preg_quote('<p><img src="about:blank" alt="attr', '/').'(?:\r\n|\r|\n)'.preg_quote('test', '/').'(?:\r\n|\r|\n)'.preg_quote('1" /></p>', '/').'/s', $output) );


		// 後始末
		$output = $this->passthru( [
			'php', __DIR__.'/../htdocs/.px_execute.php', '/?PX=clearcache'
		] );

		clearstatcache();
		$this->assertTrue( !is_dir( __DIR__.'/../htdocs/caches/p/' ) );
		$this->fs->save_file( __DIR__.'/testdata/standard/px-files/options.json', $this->testJson['relate'] );

	}



	/**
	 * 相対パスに変換し、index.htmlを付加するテスト
	 */
	public function testRelateSupply(){
		$this->fs->save_file( __DIR__.'/testdata/standard/px-files/options.json', $this->testJson['relate_supply'] );
		$output = $this->passthru( ['php', __DIR__.'/testdata/standard/.px_execute.php', '/'] );
		// var_dump($output);

		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="./abc/def/ghi.html">test1</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="./abc/def/ghi.html">test2</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<link href="./common/styles/contents.css" />', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<img src="./common/images/title.gif" alt="img1" />', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<script src="./common/scripts/contents.js"></script>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="./index.html">test3-1</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="./index.html#test">test3-2</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="./index.html?test=abc">test3-3</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="./index.html?test=abc#test">test3-4</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="./index.html?test=abc#test">test3-5</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="#test4">test4</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "./common/styles/contents1.css";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "./common/styles/contents2.css";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "./common/styles/contents3.css" all and (max-width:580px);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "./common/styles/contents4.css" all and (max-width:580px);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "./common/styles/contents1.css?time=1234567890";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "./common/styles/contents2.css?time=1234567890";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "./common/styles/contents3.css?time=1234567890" all and (max-width:580px);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "./common/styles/contents4.css?time=1234567890" all and (max-width:580px);', '/').'/s', $output) );

		$this->assertEquals( 1, preg_match('/'.preg_quote('background-image: url("./common/images/title1.gif");', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('background-image: url("./common/images/title2.gif");', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('background-image: url("./common/images/title3.gif");', '/').'/s', $output) );

		$this->assertEquals( 1, preg_match('/'.preg_quote('border-image: url(&quot;./common/images/title1.gif&quot;);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('border-image: url(&quot;./common/images/title2.gif&quot;);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('border-image: url(&quot;./common/images/title3.gif&quot;);', '/').'/s', $output) );

		$this->fs->save_file( __DIR__.'/testdata/standard/px-files/options.json', $this->testJson['relate_supply'] );
		$output = $this->passthru( ['php', __DIR__.'/testdata/standard/.px_execute.php', '/path_test_1/'] );
		// var_dump($output);

		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="../abc/def/ghi.html">test1</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="../abc/def/ghi.html">test2</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<link href="../common/styles/contents.css" />', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<img src="../common/images/title.gif" alt="img1" />', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<script src="../common/scripts/contents.js"></script>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="./index.html">test3-1</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="./index.html#test">test3-2</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="./index.html?test=abc">test3-3</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="./index.html?test=abc#test">test3-4</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="./index.html?test=abc#test">test3-5</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="#test4">test4</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "../common/styles/contents1.css";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "../common/styles/contents2.css";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "../common/styles/contents3.css" all and (max-width:580px);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "../common/styles/contents4.css" all and (max-width:580px);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "../common/styles/contents1.css?time=1234567890";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "../common/styles/contents2.css?time=1234567890";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "../common/styles/contents3.css?time=1234567890" all and (max-width:580px);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "../common/styles/contents4.css?time=1234567890" all and (max-width:580px);', '/').'/s', $output) );

		$this->assertEquals( 1, preg_match('/'.preg_quote('background-image: url("../common/images/title1.gif");', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('background-image: url("../common/images/title2.gif");', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('background-image: url("../common/images/title3.gif");', '/').'/s', $output) );

		$this->assertEquals( 1, preg_match('/'.preg_quote('border-image: url(&quot;../common/images/title1.gif&quot;);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('border-image: url(&quot;../common/images/title2.gif&quot;);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('border-image: url(&quot;../common/images/title3.gif&quot;);', '/').'/s', $output) );


		// 後始末
		$output = $this->passthru( [
			'php', __DIR__.'/../htdocs/.px_execute.php', '/?PX=clearcache'
		] );

		clearstatcache();
		$this->assertTrue( !is_dir( __DIR__.'/../htdocs/caches/p/' ) );
		$this->fs->save_file( __DIR__.'/testdata/standard/px-files/options.json', $this->testJson['relate'] );

	}

	/**
	 * 相対パスに変換し、index.htmlを削除するテスト
	 */
	public function testRelateStrip(){
		$this->fs->save_file( __DIR__.'/testdata/standard/px-files/options.json', $this->testJson['relate_strip'] );
		$output = $this->passthru( ['php', __DIR__.'/testdata/standard/.px_execute.php', '/'] );
		// var_dump($output);

		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="./abc/def/ghi.html">test1</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="./abc/def/ghi.html">test2</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<link href="./common/styles/contents.css" />', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<img src="./common/images/title.gif" alt="img1" />', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<script src="./common/scripts/contents.js"></script>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="./">test3-1</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="./#test">test3-2</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="./?test=abc">test3-3</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="./?test=abc#test">test3-4</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="./?test=abc#test">test3-5</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="#test4">test4</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "./common/styles/contents1.css";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "./common/styles/contents2.css";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "./common/styles/contents3.css" all and (max-width:580px);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "./common/styles/contents4.css" all and (max-width:580px);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "./common/styles/contents1.css?time=1234567890";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "./common/styles/contents2.css?time=1234567890";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "./common/styles/contents3.css?time=1234567890" all and (max-width:580px);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "./common/styles/contents4.css?time=1234567890" all and (max-width:580px);', '/').'/s', $output) );

		$this->assertEquals( 1, preg_match('/'.preg_quote('background-image: url("./common/images/title1.gif");', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('background-image: url("./common/images/title2.gif");', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('background-image: url("./common/images/title3.gif");', '/').'/s', $output) );

		$this->assertEquals( 1, preg_match('/'.preg_quote('border-image: url(&quot;./common/images/title1.gif&quot;);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('border-image: url(&quot;./common/images/title2.gif&quot;);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('border-image: url(&quot;./common/images/title3.gif&quot;);', '/').'/s', $output) );

		$this->fs->save_file( __DIR__.'/testdata/standard/px-files/options.json', $this->testJson['relate_supply'] );
		$output = $this->passthru( ['php', __DIR__.'/testdata/standard/.px_execute.php', '/path_test_1/'] );
		// var_dump($output);

		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="../abc/def/ghi.html">test1</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="../abc/def/ghi.html">test2</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<link href="../common/styles/contents.css" />', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<img src="../common/images/title.gif" alt="img1" />', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<script src="../common/scripts/contents.js"></script>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="./index.html">test3-1</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="./index.html#test">test3-2</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="./index.html?test=abc">test3-3</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="./index.html?test=abc#test">test3-4</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="./index.html?test=abc#test">test3-5</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="#test4">test4</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "../common/styles/contents1.css";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "../common/styles/contents2.css";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "../common/styles/contents3.css" all and (max-width:580px);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "../common/styles/contents4.css" all and (max-width:580px);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "../common/styles/contents1.css?time=1234567890";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "../common/styles/contents2.css?time=1234567890";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "../common/styles/contents3.css?time=1234567890" all and (max-width:580px);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "../common/styles/contents4.css?time=1234567890" all and (max-width:580px);', '/').'/s', $output) );

		$this->assertEquals( 1, preg_match('/'.preg_quote('background-image: url("../common/images/title1.gif");', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('background-image: url("../common/images/title2.gif");', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('background-image: url("../common/images/title3.gif");', '/').'/s', $output) );

		$this->assertEquals( 1, preg_match('/'.preg_quote('border-image: url(&quot;../common/images/title1.gif&quot;);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('border-image: url(&quot;../common/images/title2.gif&quot;);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('border-image: url(&quot;../common/images/title3.gif&quot;);', '/').'/s', $output) );


		// 後始末
		$output = $this->passthru( [
			'php', __DIR__.'/../htdocs/.px_execute.php', '/?PX=clearcache'
		] );

		clearstatcache();
		$this->assertTrue( !is_dir( __DIR__.'/../htdocs/caches/p/' ) );
		$this->fs->save_file( __DIR__.'/testdata/standard/px-files/options.json', $this->testJson['relate'] );

	}



	/**
	 * 絶対パスに変換するテスト
	 */
	public function testAbsolute(){
		$this->fs->save_file( __DIR__.'/testdata/standard/px-files/options.json', $this->testJson['absolute'] );
		$output = $this->passthru( ['php', __DIR__.'/testdata/standard/.px_execute.php', '/'] );
		// var_dump($output);

		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="/abc/def/ghi.html">test1</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="/abc/def/ghi.html">test2</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<link href="/common/styles/contents.css" />', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<img src="/common/images/title.gif" alt="img1" />', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<script src="/common/scripts/contents.js"></script>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="/">test3-1</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="/#test">test3-2</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="/?test=abc">test3-3</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="/?test=abc#test">test3-4</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="/index.html?test=abc#test">test3-5</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="#test4">test4</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents1.css";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents2.css";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents3.css" all and (max-width:580px);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents4.css" all and (max-width:580px);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents1.css?time=1234567890";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents2.css?time=1234567890";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents3.css?time=1234567890" all and (max-width:580px);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents4.css?time=1234567890" all and (max-width:580px);', '/').'/s', $output) );

		$this->assertEquals( 1, preg_match('/'.preg_quote('background-image: url("/common/images/title1.gif");', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('background-image: url("/common/images/title2.gif");', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('background-image: url("/common/images/title3.gif");', '/').'/s', $output) );

		$this->assertEquals( 1, preg_match('/'.preg_quote('border-image: url(&quot;/common/images/title1.gif&quot;);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('border-image: url(&quot;/common/images/title2.gif&quot;);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('border-image: url(&quot;/common/images/title3.gif&quot;);', '/').'/s', $output) );

		$this->fs->save_file( __DIR__.'/testdata/standard/px-files/options.json', $this->testJson['absolute'] );
		$output = $this->passthru( ['php', __DIR__.'/testdata/standard/.px_execute.php', '/path_test_1/'] );
		// var_dump($output);

		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="/abc/def/ghi.html">test1</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="/abc/def/ghi.html">test2</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<link href="/common/styles/contents.css" />', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<img src="/common/images/title.gif" alt="img1" />', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<script src="/common/scripts/contents.js"></script>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="/path_test_1/">test3-1</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="/path_test_1/#test">test3-2</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="/path_test_1/?test=abc">test3-3</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="/path_test_1/?test=abc#test">test3-4</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="/path_test_1/index.html?test=abc#test">test3-5</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="#test4">test4</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents1.css";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents2.css";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents3.css" all and (max-width:580px);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents4.css" all and (max-width:580px);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents1.css?time=1234567890";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents2.css?time=1234567890";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents3.css?time=1234567890" all and (max-width:580px);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents4.css?time=1234567890" all and (max-width:580px);', '/').'/s', $output) );

		$this->assertEquals( 1, preg_match('/'.preg_quote('background-image: url("/common/images/title1.gif");', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('background-image: url("/common/images/title2.gif");', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('background-image: url("/common/images/title3.gif");', '/').'/s', $output) );

		$this->assertEquals( 1, preg_match('/'.preg_quote('border-image: url(&quot;/common/images/title1.gif&quot;);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('border-image: url(&quot;/common/images/title2.gif&quot;);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('border-image: url(&quot;/common/images/title3.gif&quot;);', '/').'/s', $output) );


		// 後始末
		$output = $this->passthru( [
			'php', __DIR__.'/../htdocs/.px_execute.php', '/?PX=clearcache'
		] );

		clearstatcache();
		$this->assertTrue( !is_dir( __DIR__.'/../htdocs/caches/p/' ) );
		$this->fs->save_file( __DIR__.'/testdata/standard/px-files/options.json', $this->testJson['relate'] );

	}


	/**
	 * 絶対パスに変換し、index.htmlを付加するテスト
	 */
	public function testAbsoluteSupply(){
		$this->fs->save_file( __DIR__.'/testdata/standard/px-files/options.json', $this->testJson['absolute_supply'] );
		$output = $this->passthru( ['php', __DIR__.'/testdata/standard/.px_execute.php', '/'] );
		// var_dump($output);

		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="/abc/def/ghi.html">test1</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="/abc/def/ghi.html">test2</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<link href="/common/styles/contents.css" />', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<img src="/common/images/title.gif" alt="img1" />', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<script src="/common/scripts/contents.js"></script>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="/index.html">test3-1</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="/index.html#test">test3-2</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="/index.html?test=abc">test3-3</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="/index.html?test=abc#test">test3-4</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="/index.html?test=abc#test">test3-5</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="#test4">test4</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents1.css";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents2.css";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents3.css" all and (max-width:580px);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents4.css" all and (max-width:580px);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents1.css?time=1234567890";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents2.css?time=1234567890";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents3.css?time=1234567890" all and (max-width:580px);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents4.css?time=1234567890" all and (max-width:580px);', '/').'/s', $output) );

		$this->assertEquals( 1, preg_match('/'.preg_quote('background-image: url("/common/images/title1.gif");', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('background-image: url("/common/images/title2.gif");', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('background-image: url("/common/images/title3.gif");', '/').'/s', $output) );

		$this->assertEquals( 1, preg_match('/'.preg_quote('border-image: url(&quot;/common/images/title1.gif&quot;);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('border-image: url(&quot;/common/images/title2.gif&quot;);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('border-image: url(&quot;/common/images/title3.gif&quot;);', '/').'/s', $output) );

		$this->fs->save_file( __DIR__.'/testdata/standard/px-files/options.json', $this->testJson['absolute_supply'] );
		$output = $this->passthru( ['php', __DIR__.'/testdata/standard/.px_execute.php', '/path_test_1/'] );
		// var_dump($output);

		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="/abc/def/ghi.html">test1</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="/abc/def/ghi.html">test2</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<link href="/common/styles/contents.css" />', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<img src="/common/images/title.gif" alt="img1" />', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<script src="/common/scripts/contents.js"></script>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="/path_test_1/index.html">test3-1</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="/path_test_1/index.html#test">test3-2</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="/path_test_1/index.html?test=abc">test3-3</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="/path_test_1/index.html?test=abc#test">test3-4</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="/path_test_1/index.html?test=abc#test">test3-5</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="#test4">test4</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents1.css";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents2.css";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents3.css" all and (max-width:580px);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents4.css" all and (max-width:580px);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents1.css?time=1234567890";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents2.css?time=1234567890";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents3.css?time=1234567890" all and (max-width:580px);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents4.css?time=1234567890" all and (max-width:580px);', '/').'/s', $output) );

		$this->assertEquals( 1, preg_match('/'.preg_quote('background-image: url("/common/images/title1.gif");', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('background-image: url("/common/images/title2.gif");', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('background-image: url("/common/images/title3.gif");', '/').'/s', $output) );

		$this->assertEquals( 1, preg_match('/'.preg_quote('border-image: url(&quot;/common/images/title1.gif&quot;);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('border-image: url(&quot;/common/images/title2.gif&quot;);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('border-image: url(&quot;/common/images/title3.gif&quot;);', '/').'/s', $output) );


		// 後始末
		$output = $this->passthru( [
			'php', __DIR__.'/../htdocs/.px_execute.php', '/?PX=clearcache'
		] );

		clearstatcache();
		$this->assertTrue( !is_dir( __DIR__.'/../htdocs/caches/p/' ) );
		$this->fs->save_file( __DIR__.'/testdata/standard/px-files/options.json', $this->testJson['relate'] );

	}


	/**
	 * 絶対パスに変換し、index.htmlを削除するテスト
	 */
	public function testAbsoluteStrip(){
		$this->fs->save_file( __DIR__.'/testdata/standard/px-files/options.json', $this->testJson['absolute_strip'] );
		$output = $this->passthru( ['php', __DIR__.'/testdata/standard/.px_execute.php', '/'] );
		// var_dump($output);

		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="/abc/def/ghi.html">test1</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="/abc/def/ghi.html">test2</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<link href="/common/styles/contents.css" />', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<img src="/common/images/title.gif" alt="img1" />', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<script src="/common/scripts/contents.js"></script>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="/">test3-1</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="/#test">test3-2</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="/?test=abc">test3-3</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="/?test=abc#test">test3-4</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="/?test=abc#test">test3-5</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="#test4">test4</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents1.css";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents2.css";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents3.css" all and (max-width:580px);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents4.css" all and (max-width:580px);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents1.css?time=1234567890";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents2.css?time=1234567890";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents3.css?time=1234567890" all and (max-width:580px);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents4.css?time=1234567890" all and (max-width:580px);', '/').'/s', $output) );

		$this->assertEquals( 1, preg_match('/'.preg_quote('background-image: url("/common/images/title1.gif");', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('background-image: url("/common/images/title2.gif");', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('background-image: url("/common/images/title3.gif");', '/').'/s', $output) );

		$this->assertEquals( 1, preg_match('/'.preg_quote('border-image: url(&quot;/common/images/title1.gif&quot;);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('border-image: url(&quot;/common/images/title2.gif&quot;);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('border-image: url(&quot;/common/images/title3.gif&quot;);', '/').'/s', $output) );

		$this->fs->save_file( __DIR__.'/testdata/standard/px-files/options.json', $this->testJson['absolute_supply'] );
		$output = $this->passthru( ['php', __DIR__.'/testdata/standard/.px_execute.php', '/path_test_1/'] );
		// var_dump($output);

		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="/abc/def/ghi.html">test1</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="/abc/def/ghi.html">test2</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<link href="/common/styles/contents.css" />', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<img src="/common/images/title.gif" alt="img1" />', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<script src="/common/scripts/contents.js"></script>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="/path_test_1/index.html">test3-1</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="/path_test_1/index.html#test">test3-2</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="/path_test_1/index.html?test=abc">test3-3</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="/path_test_1/index.html?test=abc#test">test3-4</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="/path_test_1/index.html?test=abc#test">test3-5</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="#test4">test4</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents1.css";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents2.css";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents3.css" all and (max-width:580px);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents4.css" all and (max-width:580px);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents1.css?time=1234567890";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents2.css?time=1234567890";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents3.css?time=1234567890" all and (max-width:580px);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents4.css?time=1234567890" all and (max-width:580px);', '/').'/s', $output) );

		$this->assertEquals( 1, preg_match('/'.preg_quote('background-image: url("/common/images/title1.gif");', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('background-image: url("/common/images/title2.gif");', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('background-image: url("/common/images/title3.gif");', '/').'/s', $output) );

		$this->assertEquals( 1, preg_match('/'.preg_quote('border-image: url(&quot;/common/images/title1.gif&quot;);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('border-image: url(&quot;/common/images/title2.gif&quot;);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('border-image: url(&quot;/common/images/title3.gif&quot;);', '/').'/s', $output) );


		// 後始末
		$output = $this->passthru( [
			'php', __DIR__.'/../htdocs/.px_execute.php', '/?PX=clearcache'
		] );

		clearstatcache();
		$this->assertTrue( !is_dir( __DIR__.'/../htdocs/caches/p/' ) );
		$this->fs->save_file( __DIR__.'/testdata/standard/px-files/options.json', $this->testJson['relate'] );

	}



	/**
	 * 変換passするテスト
	 */
	public function testPass(){
		$this->fs->save_file( __DIR__.'/testdata/standard/px-files/options.json', $this->testJson['pass'] );
		$output = $this->passthru( ['php', __DIR__.'/testdata/standard/.px_execute.php', '/'] );
		// var_dump($output);

		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="/abc/defDummy/../def/./ghi.html">test1</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="../../../../abc/def/../def/./ghi.html">test2</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<link href="/common/scripts/../styles/contents.css" />', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<img src="/common/scripts/../images/title.gif" alt="img1" />', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<script src="/common/aaa/../scripts/contents.js"></script>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="./">test3-1</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="./#test">test3-2</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="./?test=abc">test3-3</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="./?test=abc#test">test3-4</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="./index.html?test=abc#test">test3-5</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="#test4">test4</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/scripts/../styles/contents1.css";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/scripts/../styles/contents2.css";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/scripts/../styles/contents3.css" all and (max-width:580px);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/scripts/../styles/contents4.css" all and (max-width:580px);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/scripts/../styles/contents1.css?time=1234567890";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/scripts/../styles/contents2.css?time=1234567890";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/scripts/../styles/contents3.css?time=1234567890" all and (max-width:580px);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/scripts/../styles/contents4.css?time=1234567890" all and (max-width:580px);', '/').'/s', $output) );

		$this->assertEquals( 1, preg_match('/'.preg_quote('background-image: url("/common/scripts/../images/title1.gif");', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('background-image: url("/common/scripts/../images/title2.gif");', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('background-image: url("/common/scripts/../images/title3.gif");', '/').'/s', $output) );

		$this->assertEquals( 1, preg_match('/'.preg_quote('border-image: url(&quot;/common/scripts/../images/title1.gif&quot;);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('border-image: url(&quot;/common/scripts/../images/title2.gif&quot;);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('border-image: url(&quot;/common/scripts/../images/title3.gif&quot;);', '/').'/s', $output) );


		// 後始末
		$output = $this->passthru( [
			'php', __DIR__.'/../htdocs/.px_execute.php', '/?PX=clearcache'
		] );

		clearstatcache();
		$this->assertTrue( !is_dir( __DIR__.'/../htdocs/caches/p/' ) );
		$this->fs->save_file( __DIR__.'/testdata/standard/px-files/options.json', $this->testJson['relate'] );

	}

	/**
	 * 変換passしてindex.htmlを付加するテスト
	 */
	public function testPassSupply(){
		$this->fs->save_file( __DIR__.'/testdata/standard/px-files/options.json', $this->testJson['pass_supply'] );
		$output = $this->passthru( ['php', __DIR__.'/testdata/standard/.px_execute.php', '/'] );
		// var_dump($output);

		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="/abc/defDummy/../def/./ghi.html">test1</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="../../../../abc/def/../def/./ghi.html">test2</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<link href="/common/scripts/../styles/contents.css" />', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<img src="/common/scripts/../images/title.gif" alt="img1" />', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<script src="/common/aaa/../scripts/contents.js"></script>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="./index.html">test3-1</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="./index.html#test">test3-2</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="./index.html?test=abc">test3-3</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="./index.html?test=abc#test">test3-4</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="./index.html?test=abc#test">test3-5</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="#test4">test4</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/scripts/../styles/contents1.css";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/scripts/../styles/contents2.css";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/scripts/../styles/contents3.css" all and (max-width:580px);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/scripts/../styles/contents4.css" all and (max-width:580px);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/scripts/../styles/contents1.css?time=1234567890";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/scripts/../styles/contents2.css?time=1234567890";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/scripts/../styles/contents3.css?time=1234567890" all and (max-width:580px);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/scripts/../styles/contents4.css?time=1234567890" all and (max-width:580px);', '/').'/s', $output) );

		$this->assertEquals( 1, preg_match('/'.preg_quote('background-image: url("/common/scripts/../images/title1.gif");', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('background-image: url("/common/scripts/../images/title2.gif");', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('background-image: url("/common/scripts/../images/title3.gif");', '/').'/s', $output) );

		$this->assertEquals( 1, preg_match('/'.preg_quote('border-image: url(&quot;/common/scripts/../images/title1.gif&quot;);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('border-image: url(&quot;/common/scripts/../images/title2.gif&quot;);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('border-image: url(&quot;/common/scripts/../images/title3.gif&quot;);', '/').'/s', $output) );


		// 後始末
		$output = $this->passthru( [
			'php', __DIR__.'/../htdocs/.px_execute.php', '/?PX=clearcache'
		] );

		clearstatcache();
		$this->assertTrue( !is_dir( __DIR__.'/../htdocs/caches/p/' ) );
		$this->fs->save_file( __DIR__.'/testdata/standard/px-files/options.json', $this->testJson['relate'] );

	}

	/**
	 * 変換passしてindex.htmlを削除するテスト
	 */
	public function testPassStrip(){
		$this->fs->save_file( __DIR__.'/testdata/standard/px-files/options.json', $this->testJson['pass_strip'] );
		$output = $this->passthru( ['php', __DIR__.'/testdata/standard/.px_execute.php', '/'] );
		// var_dump($output);

		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="/abc/defDummy/../def/./ghi.html">test1</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="../../../../abc/def/../def/./ghi.html">test2</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<link href="/common/scripts/../styles/contents.css" />', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<img src="/common/scripts/../images/title.gif" alt="img1" />', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<script src="/common/aaa/../scripts/contents.js"></script>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="./">test3-1</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="./#test">test3-2</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="./?test=abc">test3-3</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="./?test=abc#test">test3-4</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="./?test=abc#test">test3-5</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="#test4">test4</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/scripts/../styles/contents1.css";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/scripts/../styles/contents2.css";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/scripts/../styles/contents3.css" all and (max-width:580px);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/scripts/../styles/contents4.css" all and (max-width:580px);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/scripts/../styles/contents1.css?time=1234567890";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/scripts/../styles/contents2.css?time=1234567890";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/scripts/../styles/contents3.css?time=1234567890" all and (max-width:580px);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/scripts/../styles/contents4.css?time=1234567890" all and (max-width:580px);', '/').'/s', $output) );

		$this->assertEquals( 1, preg_match('/'.preg_quote('background-image: url("/common/scripts/../images/title1.gif");', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('background-image: url("/common/scripts/../images/title2.gif");', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('background-image: url("/common/scripts/../images/title3.gif");', '/').'/s', $output) );

		$this->assertEquals( 1, preg_match('/'.preg_quote('border-image: url(&quot;/common/scripts/../images/title1.gif&quot;);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('border-image: url(&quot;/common/scripts/../images/title2.gif&quot;);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('border-image: url(&quot;/common/scripts/../images/title3.gif&quot;);', '/').'/s', $output) );


		// 後始末
		$output = $this->passthru( [
			'php', __DIR__.'/../htdocs/.px_execute.php', '/?PX=clearcache'
		] );

		clearstatcache();
		$this->assertTrue( !is_dir( __DIR__.'/../htdocs/caches/p/' ) );
		$this->fs->save_file( __DIR__.'/testdata/standard/px-files/options.json', $this->testJson['relate'] );

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
