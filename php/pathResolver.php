<?php
/**
 * pickles2/px2-path-resolver pathResolver class
 */
namespace tomk79\pickles2\pathResolver;

/**
 * pickles2/px2-path-resolver pathResolver class
 */
class pathResolver{
	private $px, $options;

	/**
	 * constructor
	 */
	public function __construct( $px, $options ){
		require_once(__DIR__.'/simple_html_dom.php');
		$this->px = $px;
		if( is_string($options) ){
			$this->options['to'] = $options;
		}elseif( is_object($options) || is_array($options) ){
			$this->options = $options;
		}
		$this->options = json_decode(json_encode($options));
	}

	/**
	 * resolve path
	 */
	public function resolve(){

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
		$detect_encoding = mb_detect_encoding(''.$src);


		$is_large_content = (strlen(''.$src) > 600*1000);

		// HTMLをパース
		if($is_large_content){
			set_time_limit(1200);
		}
		$html = str_get_html(
			mb_convert_encoding( $src, DEFAULT_TARGET_CHARSET, $detect_encoding ) ,
			false, // $lowercase
			false, // $forceTagsClosed
			DEFAULT_TARGET_CHARSET, // $target_charset
			false, // $stripRN
			DEFAULT_BR_TEXT, // $defaultBRText
			DEFAULT_SPAN_TEXT // $defaultSpanText
		);
		if($is_large_content){
			set_time_limit(30);
		}

		if($html === false){
			// HTMLパースに失敗した場合、無加工のまま返す。
			$this->px->error('HTML Parse ERROR. $src size '.strlen(''.$src).' byte(s) given; '.__FILE__.' ('.__LINE__.')');
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
		$cd = $this->px->href( $this->px->req()->get_request_file_path() );
		$cd = preg_replace( '/^(.*)(\/.*?)$/si', '$1', $cd );
		if( !strlen($cd) ){
			$cd = '/';
		}

		$path_parts = parse_url($path);
		if( array_key_exists('path', $path_parts) ){
			$path = $path_parts['path'];
		}

		switch(strtolower($this->options->to)){
			case 'relate':
				// 相対パスへ変換
				$path = $this->px->fs()->get_realpath($path, $cd);
				$path = $this->px->fs()->get_relatedpath($path, $cd);
				break;
			case 'absolute':
				// 絶対パスへ変換
				$path = $this->px->fs()->get_realpath($path, $cd);
				break;
			case 'pass':
			default:
				// 処理を行わない
				break;
		}

		$path = $this->px->fs()->normalize_path($path);

		if( @is_null($this->options->supply_index_filename) ){
			// null なら処理しない
		}elseif( $this->options->supply_index_filename ){
			// 省略されたインデックスファイル名を付与
			$path = preg_replace('/\/((?:\?|\#).*)?$/si','/'.$this->px->get_directory_index_primary().'$1',$path);
		}else{
			// 省略できるインデックスファイル名を削除
			$path = preg_replace('/\/(?:'.$this->px->get_directory_index_preg_pattern().')((?:\?|\#).*)?$/si','/$1',$path);
		}


		if( array_key_exists('query', $path_parts) ){
			$path .= '?'.$path_parts['query'];
		}
		if( array_key_exists('fragment', $path_parts) ){
			$path .= '#'.$path_parts['fragment'];
		}

		return $path;
	}

}
