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
	 * 共通コンテンツのパス解決
	 */
	public function testResolveCommonContents(){
		$this->fs->copy( __DIR__.'/testdata/standard/px-files/testconfs/config_resolve_common_contents.php', __DIR__.'/testdata/standard/px-files/config.php' );
		// $this->fs->save_file( __DIR__.'/testdata/standard/px-files/options.json', $this->testJson['relate'] );
		$output = $this->passthru( ['php', __DIR__.'/testdata/standard/.px_execute.php', '/'] );
		// var_dump($output);

		// このパターンは、変換器を通らないのが正解
		// (path と content の値が一致するため)
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="/abc/defDummy/../def/./ghi.html">test1</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="../../../../abc/def/../def/./ghi.html">test2</a>', '/').'/s', $output) );

		// 変換してはいけないパスのパターン
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="http://www.pxt.jp/ja/diary/">http://url/</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="https://www.pxt.jp/ja/diary/">https://url/</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="//www.pxt.jp/ja/diary/">//url/</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAQAAAAECAMAAACeL25MAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyhpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDE0IDc5LjE1Njc5NywgMjAxNC8wOC8yMC0wOTo1MzowMiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTQgKE1hY2ludG9zaCkiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6RUZBREVFNkZBOTFDMTFFNUEzQUQ4OUIxMzVBQ0ZFMzUiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6RUZBREVFNzBBOTFDMTFFNUEzQUQ4OUIxMzVBQ0ZFMzUiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpFRkFERUU2REE5MUMxMUU1QTNBRDg5QjEzNUFDRkUzNSIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpFRkFERUU2RUE5MUMxMUU1QTNBRDg5QjEzNUFDRkUzNSIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PmwrsdQAAAAGUExURS+h5wAAAFVywYsAAAAOSURBVHjaYmDABAABBgAAFAABaEkyYwAAAABJRU5ErkJggg==">data scheme</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="file:///www/htdocs/index.html">file scheme</a>', '/').'/s', $output) );


		// $this->fs->save_file( __DIR__.'/testdata/standard/px-files/options.json', $this->testJson['relate'] );
		$output = $this->passthru( ['php', __DIR__.'/testdata/standard/.px_execute.php', '/path_test_1/'] );
		// var_dump($output);

		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="/abc/def/ghi.html">test1</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="../abc/def/ghi.html">test2</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<link href="/common/styles/contents.css" />', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<img src="/common/images/title.gif" alt="img1" />', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<script src="/common/scripts/contents.js"></script>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="../">test3-1</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="../#test">test3-2</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="../?test=abc">test3-3</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="../?test=abc#test">test3-4</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="../index.html?test=abc#test">test3-5</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="#test4">test4</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents1.css";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents2.css";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents3.css" all and (max-width:580px);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents4.css" all and (max-width:580px);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents1.css?time=1234567890";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents2.css?time=1234567890";', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents3.css?time=1234567890" all and (max-width:580px);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import "/common/styles/contents4.css?time=1234567890" all and (max-width:580px);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import url("/common/styles/url_contents1.css");', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import url("/common/styles/url_contents2.css");', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import url("/common/styles/url_contents3.css");', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import url("/common/styles/url_contents4.css");', '/').'/s', $output) );

		$this->assertEquals( 1, preg_match('/'.preg_quote('background-image: url("/common/images/title1.gif");', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('background-image: url("/common/images/title2.gif");', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('background-image: url("/common/images/title3.gif");', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('background-image: url("../common/images/title4.gif");', '/').'/s', $output) );

		$this->assertEquals( 1, preg_match('/'.preg_quote('border-image: url(&quot;/common/images/title1.gif&quot;);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('border-image: url(&quot;/common/images/title2.gif&quot;);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('border-image: url(&quot;/common/images/title3.gif&quot;);', '/').'/s', $output) );

		// 属性値中の改行を削除してしまわないことを確認するテスト。
		$this->assertEquals( 1, preg_match('/'.preg_quote('<p><img src="about:blank" alt="attr', '/').'(?:\r\n|\r|\n)'.preg_quote('test', '/').'(?:\r\n|\r|\n)'.preg_quote('1" /></p>', '/').'/s', $output) );

		// 変換してはいけないパスのパターン
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="http://www.pxt.jp/ja/diary/">http://url/</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="https://www.pxt.jp/ja/diary/">https://url/</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="//www.pxt.jp/ja/diary/">//url/</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAQAAAAECAMAAACeL25MAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyhpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDE0IDc5LjE1Njc5NywgMjAxNC8wOC8yMC0wOTo1MzowMiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTQgKE1hY2ludG9zaCkiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6RUZBREVFNkZBOTFDMTFFNUEzQUQ4OUIxMzVBQ0ZFMzUiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6RUZBREVFNzBBOTFDMTFFNUEzQUQ4OUIxMzVBQ0ZFMzUiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpFRkFERUU2REE5MUMxMUU1QTNBRDg5QjEzNUFDRkUzNSIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpFRkFERUU2RUE5MUMxMUU1QTNBRDg5QjEzNUFDRkUzNSIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PmwrsdQAAAAGUExURS+h5wAAAFVywYsAAAAOSURBVHjaYmDABAABBgAAFAABaEkyYwAAAABJRU5ErkJggg==">data scheme</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="file:///www/htdocs/index.html">file scheme</a>', '/').'/s', $output) );

		// 0バイトのコンテンツを処理するテスト
		$output = $this->passthru( ['php', __DIR__.'/testdata/standard/.px_execute.php', '/path_test_0bite/index.html'] );
		// var_dump($output);
		$this->assertTrue( $this->common_error( $output ) );

		// サイトマップに記載がないがコンテンツファイル自体は存在しているコンテンツを処理するテスト
		$output = $this->passthru( ['php', __DIR__.'/testdata/standard/.px_execute.php', '/not_defined_in_sitemap.html'] );
		// var_dump($output);
		$this->assertTrue( $this->common_error( $output ) );

		// 存在しないコンテンツを処理するテスト
		$output = $this->passthru( ['php', __DIR__.'/testdata/standard/.px_execute.php', '/path_test_not_exists/index.html'] );
		// var_dump($output);
		$this->assertTrue( $this->common_error( $output ) );


		// 後始末
		$output = $this->passthru( [
			'php', __DIR__.'/testdata/standard/.px_execute.php', '/?PX=clearcache'
		] );

		clearstatcache();
		$this->assertTrue( !is_dir( __DIR__.'/testdata/standard/caches/p/' ) );
		$this->fs->save_file( __DIR__.'/testdata/standard/px-files/options.json', $this->testJson['relate'] );

	}



	/**
	 * 相対パスに変換するテスト
	 */
	public function testRelate(){
		$this->fs->copy( __DIR__.'/testdata/standard/px-files/testconfs/config_exec.php', __DIR__.'/testdata/standard/px-files/config.php' );
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
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import url("./common/styles/url_contents1.css");', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import url("./common/styles/url_contents2.css");', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import url("./common/styles/url_contents3.css");', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import url("./common/styles/url_contents4.css");', '/').'/s', $output) );

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
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import url("../common/styles/url_contents1.css");', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import url("../common/styles/url_contents2.css");', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import url("../common/styles/url_contents3.css");', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('@import url("../common/styles/url_contents4.css");', '/').'/s', $output) );

		$this->assertEquals( 1, preg_match('/'.preg_quote('background-image: url("../common/images/title1.gif");', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('background-image: url("../common/images/title2.gif");', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('background-image: url("../common/images/title3.gif");', '/').'/s', $output) );

		$this->assertEquals( 1, preg_match('/'.preg_quote('border-image: url(&quot;../common/images/title1.gif&quot;);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('border-image: url(&quot;../common/images/title2.gif&quot;);', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('border-image: url(&quot;../common/images/title3.gif&quot;);', '/').'/s', $output) );

		// 属性値中の改行を削除してしまわないことを確認するテスト。
		$this->assertEquals( 1, preg_match('/'.preg_quote('<p><img src="about:blank" alt="attr', '/').'(?:\r\n|\r|\n)'.preg_quote('test', '/').'(?:\r\n|\r|\n)'.preg_quote('1" /></p>', '/').'/s', $output) );

		// 変換してはいけないパスのパターン
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="http://www.pxt.jp/ja/diary/">http://url/</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="https://www.pxt.jp/ja/diary/">https://url/</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="//www.pxt.jp/ja/diary/">//url/</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAQAAAAECAMAAACeL25MAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyhpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDE0IDc5LjE1Njc5NywgMjAxNC8wOC8yMC0wOTo1MzowMiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTQgKE1hY2ludG9zaCkiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6RUZBREVFNkZBOTFDMTFFNUEzQUQ4OUIxMzVBQ0ZFMzUiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6RUZBREVFNzBBOTFDMTFFNUEzQUQ4OUIxMzVBQ0ZFMzUiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpFRkFERUU2REE5MUMxMUU1QTNBRDg5QjEzNUFDRkUzNSIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpFRkFERUU2RUE5MUMxMUU1QTNBRDg5QjEzNUFDRkUzNSIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PmwrsdQAAAAGUExURS+h5wAAAFVywYsAAAAOSURBVHjaYmDABAABBgAAFAABaEkyYwAAAABJRU5ErkJggg==">data scheme</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="file:///www/htdocs/index.html">file scheme</a>', '/').'/s', $output) );

		// 0バイトのコンテンツを処理するテスト
		$output = $this->passthru( ['php', __DIR__.'/testdata/standard/.px_execute.php', '/path_test_0bite/index.html'] );
		// var_dump($output);
		$this->assertTrue( $this->common_error( $output ) );

		// サイトマップに記載がないがコンテンツファイル自体は存在しているコンテンツを処理するテスト
		$output = $this->passthru( ['php', __DIR__.'/testdata/standard/.px_execute.php', '/not_defined_in_sitemap.html'] );
		// var_dump($output);
		$this->assertTrue( $this->common_error( $output ) );

		// 存在しないコンテンツを処理するテスト
		$output = $this->passthru( ['php', __DIR__.'/testdata/standard/.px_execute.php', '/path_test_not_exists/index.html'] );
		// var_dump($output);
		$this->assertTrue( $this->common_error( $output ) );


		// 後始末
		$output = $this->passthru( [
			'php', __DIR__.'/testdata/standard/.px_execute.php', '/?PX=clearcache'
		] );

		clearstatcache();
		$this->assertTrue( !is_dir( __DIR__.'/testdata/standard/caches/p/' ) );
		$this->fs->save_file( __DIR__.'/testdata/standard/px-files/options.json', $this->testJson['relate'] );

	}



	/**
	 * 相対パスに変換し、index.htmlを付加するテスト
	 */
	public function testRelateSupply(){
		$this->fs->copy( __DIR__.'/testdata/standard/px-files/testconfs/config_exec.php', __DIR__.'/testdata/standard/px-files/config.php' );
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

		// 変換してはいけないパスのパターン
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="http://www.pxt.jp/ja/diary/">http://url/</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="https://www.pxt.jp/ja/diary/">https://url/</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="//www.pxt.jp/ja/diary/">//url/</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAQAAAAECAMAAACeL25MAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyhpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDE0IDc5LjE1Njc5NywgMjAxNC8wOC8yMC0wOTo1MzowMiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTQgKE1hY2ludG9zaCkiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6RUZBREVFNkZBOTFDMTFFNUEzQUQ4OUIxMzVBQ0ZFMzUiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6RUZBREVFNzBBOTFDMTFFNUEzQUQ4OUIxMzVBQ0ZFMzUiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpFRkFERUU2REE5MUMxMUU1QTNBRDg5QjEzNUFDRkUzNSIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpFRkFERUU2RUE5MUMxMUU1QTNBRDg5QjEzNUFDRkUzNSIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PmwrsdQAAAAGUExURS+h5wAAAFVywYsAAAAOSURBVHjaYmDABAABBgAAFAABaEkyYwAAAABJRU5ErkJggg==">data scheme</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="file:///www/htdocs/index.html">file scheme</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="#hash">#hash</a>', '/').'/s', $output) );


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


		// 変換してはいけないパスのパターン
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="http://www.pxt.jp/ja/diary/">http://url/</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="https://www.pxt.jp/ja/diary/">https://url/</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="//www.pxt.jp/ja/diary/">//url/</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAQAAAAECAMAAACeL25MAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyhpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDE0IDc5LjE1Njc5NywgMjAxNC8wOC8yMC0wOTo1MzowMiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTQgKE1hY2ludG9zaCkiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6RUZBREVFNkZBOTFDMTFFNUEzQUQ4OUIxMzVBQ0ZFMzUiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6RUZBREVFNzBBOTFDMTFFNUEzQUQ4OUIxMzVBQ0ZFMzUiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpFRkFERUU2REE5MUMxMUU1QTNBRDg5QjEzNUFDRkUzNSIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpFRkFERUU2RUE5MUMxMUU1QTNBRDg5QjEzNUFDRkUzNSIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PmwrsdQAAAAGUExURS+h5wAAAFVywYsAAAAOSURBVHjaYmDABAABBgAAFAABaEkyYwAAAABJRU5ErkJggg==">data scheme</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="file:///www/htdocs/index.html">file scheme</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="#hash">#hash</a>', '/').'/s', $output) );


		// 0バイトのコンテンツを処理するテスト
		$output = $this->passthru( ['php', __DIR__.'/testdata/standard/.px_execute.php', '/path_test_0bite/index.html'] );
		// var_dump($output);
		$this->assertTrue( $this->common_error( $output ) );

		// サイトマップに記載がないがコンテンツファイル自体は存在しているコンテンツを処理するテスト
		$output = $this->passthru( ['php', __DIR__.'/testdata/standard/.px_execute.php', '/not_defined_in_sitemap.html'] );
		// var_dump($output);
		$this->assertTrue( $this->common_error( $output ) );

		// 存在しないコンテンツを処理するテスト
		$output = $this->passthru( ['php', __DIR__.'/testdata/standard/.px_execute.php', '/path_test_not_exists/index.html'] );
		// var_dump($output);
		$this->assertTrue( $this->common_error( $output ) );


		// 後始末
		$output = $this->passthru( [
			'php', __DIR__.'/testdata/standard/.px_execute.php', '/?PX=clearcache'
		] );

		clearstatcache();
		$this->assertTrue( !is_dir( __DIR__.'/testdata/standard/caches/p/' ) );
		$this->fs->save_file( __DIR__.'/testdata/standard/px-files/options.json', $this->testJson['relate'] );

	}

	/**
	 * 相対パスに変換し、index.htmlを削除するテスト
	 */
	public function testRelateStrip(){
		$this->fs->copy( __DIR__.'/testdata/standard/px-files/testconfs/config_exec.php', __DIR__.'/testdata/standard/px-files/config.php' );
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

		// 変換してはいけないパスのパターン
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="http://www.pxt.jp/ja/diary/">http://url/</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="https://www.pxt.jp/ja/diary/">https://url/</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="//www.pxt.jp/ja/diary/">//url/</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAQAAAAECAMAAACeL25MAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyhpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDE0IDc5LjE1Njc5NywgMjAxNC8wOC8yMC0wOTo1MzowMiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTQgKE1hY2ludG9zaCkiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6RUZBREVFNkZBOTFDMTFFNUEzQUQ4OUIxMzVBQ0ZFMzUiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6RUZBREVFNzBBOTFDMTFFNUEzQUQ4OUIxMzVBQ0ZFMzUiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpFRkFERUU2REE5MUMxMUU1QTNBRDg5QjEzNUFDRkUzNSIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpFRkFERUU2RUE5MUMxMUU1QTNBRDg5QjEzNUFDRkUzNSIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PmwrsdQAAAAGUExURS+h5wAAAFVywYsAAAAOSURBVHjaYmDABAABBgAAFAABaEkyYwAAAABJRU5ErkJggg==">data scheme</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="file:///www/htdocs/index.html">file scheme</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="#hash">#hash</a>', '/').'/s', $output) );


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


		// 変換してはいけないパスのパターン
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="http://www.pxt.jp/ja/diary/">http://url/</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="https://www.pxt.jp/ja/diary/">https://url/</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="//www.pxt.jp/ja/diary/">//url/</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAQAAAAECAMAAACeL25MAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyhpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDE0IDc5LjE1Njc5NywgMjAxNC8wOC8yMC0wOTo1MzowMiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTQgKE1hY2ludG9zaCkiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6RUZBREVFNkZBOTFDMTFFNUEzQUQ4OUIxMzVBQ0ZFMzUiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6RUZBREVFNzBBOTFDMTFFNUEzQUQ4OUIxMzVBQ0ZFMzUiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpFRkFERUU2REE5MUMxMUU1QTNBRDg5QjEzNUFDRkUzNSIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpFRkFERUU2RUE5MUMxMUU1QTNBRDg5QjEzNUFDRkUzNSIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PmwrsdQAAAAGUExURS+h5wAAAFVywYsAAAAOSURBVHjaYmDABAABBgAAFAABaEkyYwAAAABJRU5ErkJggg==">data scheme</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="file:///www/htdocs/index.html">file scheme</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="#hash">#hash</a>', '/').'/s', $output) );


		// 0バイトのコンテンツを処理するテスト
		$output = $this->passthru( ['php', __DIR__.'/testdata/standard/.px_execute.php', '/path_test_0bite/index.html'] );
		// var_dump($output);
		$this->assertTrue( $this->common_error( $output ) );

		// サイトマップに記載がないがコンテンツファイル自体は存在しているコンテンツを処理するテスト
		$output = $this->passthru( ['php', __DIR__.'/testdata/standard/.px_execute.php', '/not_defined_in_sitemap.html'] );
		// var_dump($output);
		$this->assertTrue( $this->common_error( $output ) );

		// 存在しないコンテンツを処理するテスト
		$output = $this->passthru( ['php', __DIR__.'/testdata/standard/.px_execute.php', '/path_test_not_exists/index.html'] );
		// var_dump($output);
		$this->assertTrue( $this->common_error( $output ) );


		// 後始末
		$output = $this->passthru( [
			'php', __DIR__.'/testdata/standard/.px_execute.php', '/?PX=clearcache'
		] );

		clearstatcache();
		$this->assertTrue( !is_dir( __DIR__.'/testdata/standard/caches/p/' ) );
		$this->fs->save_file( __DIR__.'/testdata/standard/px-files/options.json', $this->testJson['relate'] );

	}



	/**
	 * 絶対パスに変換するテスト
	 */
	public function testAbsolute(){
		$this->fs->copy( __DIR__.'/testdata/standard/px-files/testconfs/config_exec.php', __DIR__.'/testdata/standard/px-files/config.php' );
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

		// 変換してはいけないパスのパターン
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="http://www.pxt.jp/ja/diary/">http://url/</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="https://www.pxt.jp/ja/diary/">https://url/</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="//www.pxt.jp/ja/diary/">//url/</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAQAAAAECAMAAACeL25MAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyhpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDE0IDc5LjE1Njc5NywgMjAxNC8wOC8yMC0wOTo1MzowMiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTQgKE1hY2ludG9zaCkiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6RUZBREVFNkZBOTFDMTFFNUEzQUQ4OUIxMzVBQ0ZFMzUiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6RUZBREVFNzBBOTFDMTFFNUEzQUQ4OUIxMzVBQ0ZFMzUiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpFRkFERUU2REE5MUMxMUU1QTNBRDg5QjEzNUFDRkUzNSIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpFRkFERUU2RUE5MUMxMUU1QTNBRDg5QjEzNUFDRkUzNSIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PmwrsdQAAAAGUExURS+h5wAAAFVywYsAAAAOSURBVHjaYmDABAABBgAAFAABaEkyYwAAAABJRU5ErkJggg==">data scheme</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="file:///www/htdocs/index.html">file scheme</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="#hash">#hash</a>', '/').'/s', $output) );


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


		// 変換してはいけないパスのパターン
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="http://www.pxt.jp/ja/diary/">http://url/</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="https://www.pxt.jp/ja/diary/">https://url/</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="//www.pxt.jp/ja/diary/">//url/</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAQAAAAECAMAAACeL25MAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyhpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDE0IDc5LjE1Njc5NywgMjAxNC8wOC8yMC0wOTo1MzowMiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTQgKE1hY2ludG9zaCkiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6RUZBREVFNkZBOTFDMTFFNUEzQUQ4OUIxMzVBQ0ZFMzUiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6RUZBREVFNzBBOTFDMTFFNUEzQUQ4OUIxMzVBQ0ZFMzUiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpFRkFERUU2REE5MUMxMUU1QTNBRDg5QjEzNUFDRkUzNSIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpFRkFERUU2RUE5MUMxMUU1QTNBRDg5QjEzNUFDRkUzNSIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PmwrsdQAAAAGUExURS+h5wAAAFVywYsAAAAOSURBVHjaYmDABAABBgAAFAABaEkyYwAAAABJRU5ErkJggg==">data scheme</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="file:///www/htdocs/index.html">file scheme</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="#hash">#hash</a>', '/').'/s', $output) );


		// 0バイトのコンテンツを処理するテスト
		$output = $this->passthru( ['php', __DIR__.'/testdata/standard/.px_execute.php', '/path_test_0bite/index.html'] );
		// var_dump($output);
		$this->assertTrue( $this->common_error( $output ) );

		// サイトマップに記載がないがコンテンツファイル自体は存在しているコンテンツを処理するテスト
		$output = $this->passthru( ['php', __DIR__.'/testdata/standard/.px_execute.php', '/not_defined_in_sitemap.html'] );
		// var_dump($output);
		$this->assertTrue( $this->common_error( $output ) );

		// 存在しないコンテンツを処理するテスト
		$output = $this->passthru( ['php', __DIR__.'/testdata/standard/.px_execute.php', '/path_test_not_exists/index.html'] );
		// var_dump($output);
		$this->assertTrue( $this->common_error( $output ) );


		// 後始末
		$output = $this->passthru( [
			'php', __DIR__.'/testdata/standard/.px_execute.php', '/?PX=clearcache'
		] );

		clearstatcache();
		$this->assertTrue( !is_dir( __DIR__.'/testdata/standard/caches/p/' ) );
		$this->fs->save_file( __DIR__.'/testdata/standard/px-files/options.json', $this->testJson['relate'] );

	}


	/**
	 * 絶対パスに変換し、index.htmlを付加するテスト
	 */
	public function testAbsoluteSupply(){
		$this->fs->copy( __DIR__.'/testdata/standard/px-files/testconfs/config_exec.php', __DIR__.'/testdata/standard/px-files/config.php' );
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

		// 変換してはいけないパスのパターン
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="http://www.pxt.jp/ja/diary/">http://url/</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="https://www.pxt.jp/ja/diary/">https://url/</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="//www.pxt.jp/ja/diary/">//url/</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAQAAAAECAMAAACeL25MAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyhpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDE0IDc5LjE1Njc5NywgMjAxNC8wOC8yMC0wOTo1MzowMiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTQgKE1hY2ludG9zaCkiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6RUZBREVFNkZBOTFDMTFFNUEzQUQ4OUIxMzVBQ0ZFMzUiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6RUZBREVFNzBBOTFDMTFFNUEzQUQ4OUIxMzVBQ0ZFMzUiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpFRkFERUU2REE5MUMxMUU1QTNBRDg5QjEzNUFDRkUzNSIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpFRkFERUU2RUE5MUMxMUU1QTNBRDg5QjEzNUFDRkUzNSIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PmwrsdQAAAAGUExURS+h5wAAAFVywYsAAAAOSURBVHjaYmDABAABBgAAFAABaEkyYwAAAABJRU5ErkJggg==">data scheme</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="file:///www/htdocs/index.html">file scheme</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="#hash">#hash</a>', '/').'/s', $output) );


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


		// 変換してはいけないパスのパターン
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="http://www.pxt.jp/ja/diary/">http://url/</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="https://www.pxt.jp/ja/diary/">https://url/</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="//www.pxt.jp/ja/diary/">//url/</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAQAAAAECAMAAACeL25MAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyhpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDE0IDc5LjE1Njc5NywgMjAxNC8wOC8yMC0wOTo1MzowMiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTQgKE1hY2ludG9zaCkiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6RUZBREVFNkZBOTFDMTFFNUEzQUQ4OUIxMzVBQ0ZFMzUiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6RUZBREVFNzBBOTFDMTFFNUEzQUQ4OUIxMzVBQ0ZFMzUiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpFRkFERUU2REE5MUMxMUU1QTNBRDg5QjEzNUFDRkUzNSIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpFRkFERUU2RUE5MUMxMUU1QTNBRDg5QjEzNUFDRkUzNSIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PmwrsdQAAAAGUExURS+h5wAAAFVywYsAAAAOSURBVHjaYmDABAABBgAAFAABaEkyYwAAAABJRU5ErkJggg==">data scheme</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="file:///www/htdocs/index.html">file scheme</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="#hash">#hash</a>', '/').'/s', $output) );


		// 0バイトのコンテンツを処理するテスト
		$output = $this->passthru( ['php', __DIR__.'/testdata/standard/.px_execute.php', '/path_test_0bite/index.html'] );
		// var_dump($output);
		$this->assertTrue( $this->common_error( $output ) );

		// サイトマップに記載がないがコンテンツファイル自体は存在しているコンテンツを処理するテスト
		$output = $this->passthru( ['php', __DIR__.'/testdata/standard/.px_execute.php', '/not_defined_in_sitemap.html'] );
		// var_dump($output);
		$this->assertTrue( $this->common_error( $output ) );

		// 存在しないコンテンツを処理するテスト
		$output = $this->passthru( ['php', __DIR__.'/testdata/standard/.px_execute.php', '/path_test_not_exists/index.html'] );
		// var_dump($output);
		$this->assertTrue( $this->common_error( $output ) );


		// 後始末
		$output = $this->passthru( [
			'php', __DIR__.'/testdata/standard/.px_execute.php', '/?PX=clearcache'
		] );

		clearstatcache();
		$this->assertTrue( !is_dir( __DIR__.'/testdata/standard/caches/p/' ) );
		$this->fs->save_file( __DIR__.'/testdata/standard/px-files/options.json', $this->testJson['relate'] );

	}


	/**
	 * 絶対パスに変換し、index.htmlを削除するテスト
	 */
	public function testAbsoluteStrip(){
		$this->fs->copy( __DIR__.'/testdata/standard/px-files/testconfs/config_exec.php', __DIR__.'/testdata/standard/px-files/config.php' );
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

		// 変換してはいけないパスのパターン
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="http://www.pxt.jp/ja/diary/">http://url/</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="https://www.pxt.jp/ja/diary/">https://url/</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="//www.pxt.jp/ja/diary/">//url/</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAQAAAAECAMAAACeL25MAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyhpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDE0IDc5LjE1Njc5NywgMjAxNC8wOC8yMC0wOTo1MzowMiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTQgKE1hY2ludG9zaCkiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6RUZBREVFNkZBOTFDMTFFNUEzQUQ4OUIxMzVBQ0ZFMzUiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6RUZBREVFNzBBOTFDMTFFNUEzQUQ4OUIxMzVBQ0ZFMzUiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpFRkFERUU2REE5MUMxMUU1QTNBRDg5QjEzNUFDRkUzNSIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpFRkFERUU2RUE5MUMxMUU1QTNBRDg5QjEzNUFDRkUzNSIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PmwrsdQAAAAGUExURS+h5wAAAFVywYsAAAAOSURBVHjaYmDABAABBgAAFAABaEkyYwAAAABJRU5ErkJggg==">data scheme</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="file:///www/htdocs/index.html">file scheme</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="#hash">#hash</a>', '/').'/s', $output) );


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


		// 変換してはいけないパスのパターン
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="http://www.pxt.jp/ja/diary/">http://url/</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="https://www.pxt.jp/ja/diary/">https://url/</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="//www.pxt.jp/ja/diary/">//url/</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAQAAAAECAMAAACeL25MAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyhpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDE0IDc5LjE1Njc5NywgMjAxNC8wOC8yMC0wOTo1MzowMiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTQgKE1hY2ludG9zaCkiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6RUZBREVFNkZBOTFDMTFFNUEzQUQ4OUIxMzVBQ0ZFMzUiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6RUZBREVFNzBBOTFDMTFFNUEzQUQ4OUIxMzVBQ0ZFMzUiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpFRkFERUU2REE5MUMxMUU1QTNBRDg5QjEzNUFDRkUzNSIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpFRkFERUU2RUE5MUMxMUU1QTNBRDg5QjEzNUFDRkUzNSIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PmwrsdQAAAAGUExURS+h5wAAAFVywYsAAAAOSURBVHjaYmDABAABBgAAFAABaEkyYwAAAABJRU5ErkJggg==">data scheme</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="file:///www/htdocs/index.html">file scheme</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="#hash">#hash</a>', '/').'/s', $output) );


		// 0バイトのコンテンツを処理するテスト
		$output = $this->passthru( ['php', __DIR__.'/testdata/standard/.px_execute.php', '/path_test_0bite/index.html'] );
		// var_dump($output);
		$this->assertTrue( $this->common_error( $output ) );

		// サイトマップに記載がないがコンテンツファイル自体は存在しているコンテンツを処理するテスト
		$output = $this->passthru( ['php', __DIR__.'/testdata/standard/.px_execute.php', '/not_defined_in_sitemap.html'] );
		// var_dump($output);
		$this->assertTrue( $this->common_error( $output ) );

		// 存在しないコンテンツを処理するテスト
		$output = $this->passthru( ['php', __DIR__.'/testdata/standard/.px_execute.php', '/path_test_not_exists/index.html'] );
		// var_dump($output);
		$this->assertTrue( $this->common_error( $output ) );


		// 後始末
		$output = $this->passthru( [
			'php', __DIR__.'/testdata/standard/.px_execute.php', '/?PX=clearcache'
		] );

		clearstatcache();
		$this->assertTrue( !is_dir( __DIR__.'/testdata/standard/caches/p/' ) );
		$this->fs->save_file( __DIR__.'/testdata/standard/px-files/options.json', $this->testJson['relate'] );

	}



	/**
	 * 変換passするテスト
	 */
	public function testPass(){
		$this->fs->copy( __DIR__.'/testdata/standard/px-files/testconfs/config_exec.php', __DIR__.'/testdata/standard/px-files/config.php' );
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


		// 変換してはいけないパスのパターン
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="http://www.pxt.jp/ja/diary/">http://url/</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="https://www.pxt.jp/ja/diary/">https://url/</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="//www.pxt.jp/ja/diary/">//url/</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAQAAAAECAMAAACeL25MAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyhpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDE0IDc5LjE1Njc5NywgMjAxNC8wOC8yMC0wOTo1MzowMiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTQgKE1hY2ludG9zaCkiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6RUZBREVFNkZBOTFDMTFFNUEzQUQ4OUIxMzVBQ0ZFMzUiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6RUZBREVFNzBBOTFDMTFFNUEzQUQ4OUIxMzVBQ0ZFMzUiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpFRkFERUU2REE5MUMxMUU1QTNBRDg5QjEzNUFDRkUzNSIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpFRkFERUU2RUE5MUMxMUU1QTNBRDg5QjEzNUFDRkUzNSIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PmwrsdQAAAAGUExURS+h5wAAAFVywYsAAAAOSURBVHjaYmDABAABBgAAFAABaEkyYwAAAABJRU5ErkJggg==">data scheme</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="file:///www/htdocs/index.html">file scheme</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="#hash">#hash</a>', '/').'/s', $output) );


		// 0バイトのコンテンツを処理するテスト
		$output = $this->passthru( ['php', __DIR__.'/testdata/standard/.px_execute.php', '/path_test_0bite/index.html'] );
		// var_dump($output);
		$this->assertTrue( $this->common_error( $output ) );

		// サイトマップに記載がないがコンテンツファイル自体は存在しているコンテンツを処理するテスト
		$output = $this->passthru( ['php', __DIR__.'/testdata/standard/.px_execute.php', '/not_defined_in_sitemap.html'] );
		// var_dump($output);
		$this->assertTrue( $this->common_error( $output ) );

		// 存在しないコンテンツを処理するテスト
		$output = $this->passthru( ['php', __DIR__.'/testdata/standard/.px_execute.php', '/path_test_not_exists/index.html'] );
		// var_dump($output);
		$this->assertTrue( $this->common_error( $output ) );


		// 後始末
		$output = $this->passthru( [
			'php', __DIR__.'/testdata/standard/.px_execute.php', '/?PX=clearcache'
		] );

		clearstatcache();
		$this->assertTrue( !is_dir( __DIR__.'/testdata/standard/caches/p/' ) );
		$this->fs->save_file( __DIR__.'/testdata/standard/px-files/options.json', $this->testJson['relate'] );

	}

	/**
	 * 変換passしてindex.htmlを付加するテスト
	 */
	public function testPassSupply(){
		$this->fs->copy( __DIR__.'/testdata/standard/px-files/testconfs/config_exec.php', __DIR__.'/testdata/standard/px-files/config.php' );
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


		// 変換してはいけないパスのパターン
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="http://www.pxt.jp/ja/diary/">http://url/</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="https://www.pxt.jp/ja/diary/">https://url/</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="//www.pxt.jp/ja/diary/">//url/</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAQAAAAECAMAAACeL25MAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyhpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDE0IDc5LjE1Njc5NywgMjAxNC8wOC8yMC0wOTo1MzowMiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTQgKE1hY2ludG9zaCkiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6RUZBREVFNkZBOTFDMTFFNUEzQUQ4OUIxMzVBQ0ZFMzUiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6RUZBREVFNzBBOTFDMTFFNUEzQUQ4OUIxMzVBQ0ZFMzUiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpFRkFERUU2REE5MUMxMUU1QTNBRDg5QjEzNUFDRkUzNSIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpFRkFERUU2RUE5MUMxMUU1QTNBRDg5QjEzNUFDRkUzNSIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PmwrsdQAAAAGUExURS+h5wAAAFVywYsAAAAOSURBVHjaYmDABAABBgAAFAABaEkyYwAAAABJRU5ErkJggg==">data scheme</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="file:///www/htdocs/index.html">file scheme</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="#hash">#hash</a>', '/').'/s', $output) );


		// 0バイトのコンテンツを処理するテスト
		$output = $this->passthru( ['php', __DIR__.'/testdata/standard/.px_execute.php', '/path_test_0bite/index.html'] );
		// var_dump($output);
		$this->assertTrue( $this->common_error( $output ) );

		// サイトマップに記載がないがコンテンツファイル自体は存在しているコンテンツを処理するテスト
		$output = $this->passthru( ['php', __DIR__.'/testdata/standard/.px_execute.php', '/not_defined_in_sitemap.html'] );
		// var_dump($output);
		$this->assertTrue( $this->common_error( $output ) );

		// 存在しないコンテンツを処理するテスト
		$output = $this->passthru( ['php', __DIR__.'/testdata/standard/.px_execute.php', '/path_test_not_exists/index.html'] );
		// var_dump($output);
		$this->assertTrue( $this->common_error( $output ) );


		// 後始末
		$output = $this->passthru( [
			'php', __DIR__.'/testdata/standard/.px_execute.php', '/?PX=clearcache'
		] );

		clearstatcache();
		$this->assertTrue( !is_dir( __DIR__.'/testdata/standard/caches/p/' ) );
		$this->fs->save_file( __DIR__.'/testdata/standard/px-files/options.json', $this->testJson['relate'] );

	}

	/**
	 * 変換passしてindex.htmlを削除するテスト
	 */
	public function testPassStrip(){
		$this->fs->copy( __DIR__.'/testdata/standard/px-files/testconfs/config_exec.php', __DIR__.'/testdata/standard/px-files/config.php' );
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


		// 変換してはいけないパスのパターン
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="http://www.pxt.jp/ja/diary/">http://url/</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="https://www.pxt.jp/ja/diary/">https://url/</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="//www.pxt.jp/ja/diary/">//url/</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAQAAAAECAMAAACeL25MAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyhpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDE0IDc5LjE1Njc5NywgMjAxNC8wOC8yMC0wOTo1MzowMiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTQgKE1hY2ludG9zaCkiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6RUZBREVFNkZBOTFDMTFFNUEzQUQ4OUIxMzVBQ0ZFMzUiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6RUZBREVFNzBBOTFDMTFFNUEzQUQ4OUIxMzVBQ0ZFMzUiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpFRkFERUU2REE5MUMxMUU1QTNBRDg5QjEzNUFDRkUzNSIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpFRkFERUU2RUE5MUMxMUU1QTNBRDg5QjEzNUFDRkUzNSIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PmwrsdQAAAAGUExURS+h5wAAAFVywYsAAAAOSURBVHjaYmDABAABBgAAFAABaEkyYwAAAABJRU5ErkJggg==">data scheme</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="file:///www/htdocs/index.html">file scheme</a>', '/').'/s', $output) );
		$this->assertEquals( 1, preg_match('/'.preg_quote('<a href="#hash">#hash</a>', '/').'/s', $output) );


		// 0バイトのコンテンツを処理するテスト
		$output = $this->passthru( ['php', __DIR__.'/testdata/standard/.px_execute.php', '/path_test_0bite/index.html'] );
		// var_dump($output);
		$this->assertTrue( $this->common_error( $output ) );

		// サイトマップに記載がないがコンテンツファイル自体は存在しているコンテンツを処理するテスト
		$output = $this->passthru( ['php', __DIR__.'/testdata/standard/.px_execute.php', '/not_defined_in_sitemap.html'] );
		// var_dump($output);
		$this->assertTrue( $this->common_error( $output ) );

		// 存在しないコンテンツを処理するテスト
		$output = $this->passthru( ['php', __DIR__.'/testdata/standard/.px_execute.php', '/path_test_not_exists/index.html'] );
		// var_dump($output);
		$this->assertTrue( $this->common_error( $output ) );


		// 後始末
		$output = $this->passthru( [
			'php', __DIR__.'/testdata/standard/.px_execute.php', '/?PX=clearcache'
		] );

		clearstatcache();
		$this->assertTrue( !is_dir( __DIR__.'/testdata/standard/caches/p/' ) );
		$this->fs->save_file( __DIR__.'/testdata/standard/px-files/options.json', $this->testJson['relate'] );

	}

	/**
	 * 大きすぎてパースできないHTMLを変換にかけるテスト
	 */
	public function testTooBigHtml(){
		$this->fs->copy( __DIR__.'/testdata/standard/px-files/testconfs/config_exec.php', __DIR__.'/testdata/standard/px-files/config.php' );
		$this->fs->save_file( __DIR__.'/testdata/standard/px-files/options.json', $this->testJson['pass_strip'] );
		$output = $this->passthru( [
			'php',
			__DIR__.'/testdata/standard/.px_execute.php',
			'-o', 'json',
			'/broken.html'
		] );
		$output = json_decode($output);
		// var_dump($output->errors);

		$this->assertNotEmpty( $output->errors );

		// 後始末
		$output = $this->passthru( [
			'php', __DIR__.'/testdata/standard/.px_execute.php', '/?PX=clearcache'
		] );

		clearstatcache();
		$this->assertTrue( !is_dir( __DIR__.'/testdata/standard/caches/p/' ) );
		$this->fs->save_file( __DIR__.'/testdata/standard/px-files/options.json', $this->testJson['relate'] );

	}



	/**
	 * PHPがエラー吐いてないか確認しておく。
	 */
	private function common_error( $output ){
		if( preg_match('/'.preg_quote('Fatal', '/').'/si', $output) ){ return false; }
		if( preg_match('/'.preg_quote('Warning', '/').'/si', $output) ){ return false; }
		if( preg_match('/'.preg_quote('Notice', '/').'/si', $output) ){ return false; }
		return true;
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
