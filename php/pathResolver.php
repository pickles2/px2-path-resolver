<?php
/**
 * Pickles2 DEC CORE class
 */
namespace tomk79\pickles2\pathResolver;

/**
 * Pickles2 DEC CORE class
 */
class main{
	private $px, $options;

	/**
	 * 変換処理の実行
	 * @param object $px Picklesオブジェクト
	 */
	public static function exec( $px, $options = null ){
		// var_dump($options);
		(new self($px, $options))->resolve();
		return true;
	}

	/**
	 * constructor
	 */
	public function __construct( $px, $options ){
		$this->px = $px;
		if( is_string($options) ){
			$this->options['to'] = $options;
		}elseif( is_object($options) || is_array($options) ){
			$this->options = $options;
		}
		$this->options = json_decode(json_encode($options));
		require_once(__DIR__.'/simple_html_dom.php');
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

		// data-dec-blockブロックを削除
		$html = str_get_html(
			$src ,
			false, // $lowercase
			false, // $forceTagsClosed
			DEFAULT_TARGET_CHARSET, // $target_charset
			false, // $stripRN
			DEFAULT_BR_TEXT, // $defaultBRText
			DEFAULT_SPAN_TEXT // $defaultSpanText
		);

		$ret = $html->find('*[href]');
		foreach( $ret as $retRow ){
			$val = $retRow->getAttribute('href');
			$val = $this->get_new_path($val);
			$retRow->setAttribute('href', $val);
		}

		$ret = $html->find('*[src]');
		foreach( $ret as $retRow ){
			$val = $retRow->getAttribute('src');
			$val = $this->get_new_path($val);
			$retRow->setAttribute('src', $val);
		}

		$ret = $html->find('form[action]');
		foreach( $ret as $retRow ){
			$val = $retRow->getAttribute('action');
			$val = $this->get_new_path($val);
			$retRow->setAttribute('action', $val);
		}

		$ret = $html->find('style');
		foreach( $ret as $retRow ){
			$val = $retRow->innertext;
			$val = $this->path_resolve_in_css($val);
			$retRow->innertext = $val;
		}

		$src = $html->outertext;

		return $src;
	}

	/**
	 * CSSファイル中のパスを解決
	 */
	private function path_resolve_in_css( $bin ){

		$rtn = '';

		// url()
		while( 1 ){
			if( !preg_match( '/^(.*?)url\s*\\((.*?)\\)(.*)$/si', $bin, $matched ) ){
				$rtn .= $bin;
				break;
			}
			$rtn .= $matched[1];
			$rtn .= 'url("';
			$res = trim( $matched[2] );
			if( preg_match( '/^(\"|\')(.*)\1$/si', $res, $matched2 ) ){
				$res = trim( $matched2[2] );
			}
			$res = $this->get_new_path( $res );
			$rtn .= $res;
			$rtn .= '")';
			$bin = $matched[3];
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
			$rtn .= '@import "';
			$res = trim( $matched[2] );
			if( preg_match( '/^(\"|\')(.*)\1$/si', $res, $matched2 ) ){
				$res = trim( $matched2[2] );
			}
			$res = $this->get_new_path( $res );
			$rtn .= $res;
			$rtn .= '"';
			$bin = $matched[3];
		}

		return $rtn;
	}

	/**
	 * 変換後の新しいパスを取得
	 */
	private function get_new_path( $path ){
		if( preg_match( '/^(?:[a-zA-Z0-9]+\:|\/\/|\#)/', $path ) ){
			return $path;
		}
		$cd = $this->px->href( $this->px->req()->get_request_file_path() );
		$cd = preg_replace( '/^(.*)(\/.*?)$/si', '$1', $cd );
		if( !strlen($cd) ){
			$cd = '/';
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

		if( $this->options->supply_index_filename ){
			// 省略されたインデックスファイル名を付与
			$path = preg_replace('/\/((?:\?|\#).*)?$/si','/'.$this->px->get_directory_index_primary().'$1',$path);
		}

		return $path;
	}

}
