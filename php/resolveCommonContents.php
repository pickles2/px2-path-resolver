<?php
/**
 * pickles2/px2-path-resolver resolveCommonContents class
 */
namespace tomk79\pickles2\pathResolver;

/**
 * pickles2/px2-path-resolver resolveCommonContents class
 */
class resolveCommonContents{
	private $px, $options;
	private $page_info;

	/**
	 * constructor
	 */
	public function __construct( $px, $options ){
		$this->px = $px;
	}

	/**
	 * resolve path
	 */
	public function resolve(){

		$page_info = $this->px->site()->get_current_page_info();
		// var_dump($page_info);
		if( !is_array($page_info) ){
			// サイトマップに定義されていない場合は何もしない
			return true;
		}
		if( !array_key_exists('path', $page_info) || !array_key_exists('content', $page_info) ){
			// path または content が未定義な場合は何もしない
			return true;
		}
		if( $page_info['path'] === $page_info['content'] ){
			// path と content の値が一致する場合は何もしない
			return true;
		}
		$this->page_info = $page_info;

		$ext = $this->px->fs()->get_extension($this->px->req()->get_request_file_path());

		foreach( $this->px->bowl()->get_keys() as $key ){
			$src = $this->px->bowl()->pull( $key );

			switch( strtolower($ext) ){
				case 'html':
				case 'htm':
					$src = $this->path_resolve_in_html($src);
					break;
				case 'css':
					$src = $this->path_resolve_in_css($src);
					break;
			}

			$this->px->bowl()->replace( $src, $key );
		}
		return true;
	}

	/**
	 * HTMLファイル中のパスを解決
	 */
	private function path_resolve_in_html( $src ){

		// Simple HTML Parser を通したときに、
		// もとの文字セットが無視されて DEFAULT_TARGET_CHARSET (=UTF-8) に変換されてしまう問題に対して、
		// もとの文字セットを記憶 → UTF-8 に一時変換 → Simple HTML Parser → 最後にもとの文字セットに変換しなおす
		// という処理で対応した。
		$detect_encoding = mb_detect_encoding($src);


		// data-dec-blockブロックを削除
		$html = str_get_html(
			mb_convert_encoding( $src, DEFAULT_TARGET_CHARSET, $detect_encoding ) ,
			false, // $lowercase
			false, // $forceTagsClosed
			DEFAULT_TARGET_CHARSET, // $target_charset
			false, // $stripRN
			DEFAULT_BR_TEXT, // $defaultBRText
			DEFAULT_SPAN_TEXT // $defaultSpanText
		);

		if( $html === false ){
			return $src;
		}

		$conf_dom_selectors = array(
			'*[href]'=>'href',
			'*[src]'=>'src',
			'form[action]'=>'action',
		);

		foreach( $conf_dom_selectors as $selector=>$attr_name ){
			$ret = $html->find($selector);
			foreach( $ret as $retRow ){
				$val = $retRow->getAttribute($attr_name);
				$val = $this->get_new_path($val);
				$retRow->setAttribute($attr_name, $val);
			}
		}

		$ret = $html->find('*[style]');
		foreach( $ret as $retRow ){
			$val = $retRow->getAttribute('style');
			$val = str_replace('&quot;', '"', $val);
			$val = str_replace('&lt;', '<', $val);
			$val = str_replace('&gt;', '>', $val);
			$val = $this->path_resolve_in_css($val);
			$val = str_replace('"', '&quot;', $val);
			$val = str_replace('<', '&lt;', $val);
			$val = str_replace('>', '&gt;', $val);
			$retRow->setAttribute('style', $val);
		}

		$ret = $html->find('style');
		foreach( $ret as $retRow ){
			$val = $retRow->innertext;
			$val = $this->path_resolve_in_css($val);
			$retRow->innertext = $val;
		}

		$src = $html->outertext;

		// もとの文字セットを復元
		$src = mb_convert_encoding( $src, $detect_encoding );

		return $src;
	}

	/**
	 * CSSファイル中のパスを解決
	 */
	private function path_resolve_in_css( $bin ){

		$rtn = '';

		// url()
		while( 1 ){
			if( !preg_match( '/^(.*?)(\/\*|url\s*\\(\s*(\"|\'|))(.*)$/si', $bin, $matched ) ){
				$rtn .= $bin;
				break;
			}
			$rtn .= $matched[1];
			$start = $matched[2];
			$delimiter = $matched[3];
			$bin = $matched[4];

			if( $start == '/*' ){
				$rtn .= '/*';
				preg_match( '/^(.*?)\*\/(.*)$/si', $bin, $matched );
				$rtn .= $matched[1];
				$rtn .= '*/';
				$bin = $matched[2];
			}else{
				$rtn .= 'url("';
				preg_match( '/^(.*?)'.preg_quote($delimiter, '/').'\s*\)(.*)$/si', $bin, $matched );
				$res = trim( $matched[1] );
				$res = $this->get_new_path( $res );
				$rtn .= $res;
				$rtn .= '")';
				$bin = $matched[2];
			}

		}

		// @import
		$bin = $rtn;
		$rtn = '';
		while( 1 ){
			if( !preg_match( '/^(.*?)@import\s*([^\s\;]*)(.*)$/si', $bin, $matched ) ){
				$rtn .= $bin;
				break;
			}
			$rtn .= $matched[1];
			$rtn .= '@import ';
			$res = trim( $matched[2] );
			if( !preg_match('/^url\s*\(/', $res) ){
				$rtn .= '"';
				if( preg_match( '/^(\"|\')(.*)\1$/si', $res, $matched2 ) ){
					$res = trim( $matched2[2] );
				}
				$res = $this->get_new_path( $res );
				$rtn .= $res;
				$rtn .= '"';
			}else{
				$rtn .= $res;
			}
			$bin = $matched[3];
		}

		return $rtn;
	}

	/**
	 * 変換後の新しいパスを取得
	 */
	private function get_new_path( $path ){
		if( preg_match( '/^(?:[a-zA-Z0-9]+\:|\/\/|\#|\?)/', $path ) ){
			return $path;
		}

		$path_parts = parse_url($path);
		if( array_key_exists('path', $path_parts) ){
			$path = $path_parts['path'];
		}

		$to = 'relate';
		if( preg_match( '/^[\\/\\\\]/', $path ) ){
			$to = 'absolute';
		}

		$cd_content_based = $this->px->href( $this->page_info['content'] );
		$cd_content_based = preg_replace( '/^(.*)(\/.*?)$/si', '$1', $cd_content_based );
		if( !strlen($cd_content_based) ){
			$cd_content_based = '/';
		}

		$cd_path_based = $this->px->href( $this->px->req()->get_request_file_path() );
		$cd_path_based = preg_replace( '/^(.*)(\/.*?)$/si', '$1', $cd_path_based );
		if( !strlen($cd_path_based) ){
			$cd_path_based = '/';
		}

		// 絶対パスへ一旦変換
		$path = $this->px->fs()->get_realpath($path, $cd_content_based);

		switch(strtolower($to)){
			case 'relate':
				// 相対パスへ変換
				// $path = $this->px->fs()->get_realpath($path, $cd_path_based);
				$path = $this->px->fs()->get_relatedpath($path, $cd_path_based);
				break;
			case 'absolute':
				// 絶対パスへ変換
				$path = $this->px->fs()->get_realpath($path, $cd_path_based);
				break;
			case 'pass':
			default:
				// 処理を行わない
				break;
		}

		$path = $this->px->fs()->normalize_path($path);

		// if( @is_null($this->options->supply_index_filename) ){
		// 	// null なら処理しない
		// }elseif( $this->options->supply_index_filename ){
		// 	// 省略されたインデックスファイル名を付与
		// 	$path = preg_replace('/\/((?:\?|\#).*)?$/si','/'.$this->px->get_directory_index_primary().'$1',$path);
		// }else{
		// 	// 省略できるインデックスファイル名を削除
		// 	$path = preg_replace('/\/(?:'.$this->px->get_directory_index_preg_pattern().')((?:\?|\#).*)?$/si','/$1',$path);
		// }

		if( array_key_exists('query', $path_parts) ){
			$path .= '?'.$path_parts['query'];
		}
		if( array_key_exists('fragment', $path_parts) ){
			$path .= '#'.$path_parts['fragment'];
		}

		return $path;
	}

}
