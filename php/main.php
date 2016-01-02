<?php
/**
 * pickles2/px2-path-resolver CORE class
 */
namespace tomk79\pickles2\pathResolver;

/**
 * pickles2/px2-path-resolver CORE class
 */
class main{
	private $px, $options;

	/**
	 * 相対パス・絶対パス 変換処理の実行
	 * @param object $px Picklesオブジェクト
	 * @param array $options オプション
	 */
	public static function exec( $px, $options = null ){
		// var_dump($options);
		(new pathResolver($px, $options))->resolve();
		return true;
	}

	/**
	 * 共通コンテンツのリンクやリソースのパスを解決する
	 * @param object $px Picklesオブジェクト
	 * @param array $options オプション
	 */
	public static function resolve_common_contents( $px, $options = null ){
		// var_dump($options);
		(new resolveCommonContents($px, $options))->resolve();
		return true;
	}

}
