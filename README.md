# pickles2/px2-path-resolver


<table>
  <thead>
    <tr>
      <th></th>
      <th>Linux</th>
      <th>Windows</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <th>master</th>
      <td align="center">
        <a href="https://travis-ci.org/pickles2/px2-path-resolver"><img src="https://secure.travis-ci.org/pickles2/px2-path-resolver.svg?branch=master"></a>
      </td>
      <td align="center">
        <a href="https://ci.appveyor.com/project/pickles2/px2-path-resolver"><img src="https://ci.appveyor.com/api/projects/status/9u7o6tf510e8r7e0/branch/master?svg=true"></a>
      </td>
    </tr>
    <tr>
      <th>develop</th>
      <td align="center">
        <a href="https://travis-ci.org/pickles2/px2-path-resolver"><img src="https://secure.travis-ci.org/pickles2/px2-path-resolver.svg?branch=develop"></a>
      </td>
      <td align="center">
        <a href="https://ci.appveyor.com/project/pickles2/px2-path-resolver"><img src="https://ci.appveyor.com/api/projects/status/9u7o6tf510e8r7e0/branch/develop?svg=true"></a>
      </td>
    </tr>
  </tbody>
</table>

_px2-path-resolver_ は、[Pickles 2](http://pickles2.pxt.jp/) に、相対パス・絶対パスでの書き出しオプションを追加します。


## 導入方法 - Setup

### 1. [Pickles 2](http://pickles2.pxt.jp/) をセットアップ

### 2. composer.json に、パッケージ情報を追加

```
{
    "require": {
        "pickles2/px2-path-resolver": "2.*"
    }
}
```

### 3. composer update

更新したパッケージ情報を反映します。

```
$ composer update
```

### 4. config.php を更新

`$conf->funcs->before_output` に、プラグイン設定を追加します。

```
<?php
return call_user_func( function(){

  /* (中略) */

  // processor
  $conf->funcs->processor->html = [
    // px2-path-resolver - 共通コンテンツのリンクやリソースのパスを解決する
    //   このAPIは、サイトマップCSV上で path と content が異なるパスを参照している場合に、
    //   相対的に記述されたリンクやリソースのパスがあわなくなる問題を解決します。
    'tomk79\pickles2\pathResolver\main::resolve_common_contents()' ,

    // テーマ
    // 'theme'=>'pickles2\themes\pickles\theme::exec' ,
    'theme'=>'(API name of theme package)' ,

  ];

  /* (中略) */

  // funcs: Before output
  $conf->funcs->before_output = [
    // px2-path-resolver - 相対パス・絶対パスを変換して出力する
    //   options
    //     string 'to':
    //       - relate: 相対パスへ変換
    //       - absolute: 絶対パスへ変換
    //       - pass: 変換を行わない(default)
    //     bool 'supply_index_filename':
    //       - true: 省略されたindexファイル名を補う
    //       - false: 省略できるindexファイル名を削除
    //       - null: そのまま (default)
    'tomk79\pickles2\pathResolver\main::exec('.json_encode(array(
      'to' => 'relate',
      'supply_index_filename' => true
    )).')' ,

  ];

  /* (中略) */

  return $conf;
} );
```


## 更新履歴 - Change log

### px2-path-resolver 2.0.8 (2018年??月??日)

- `resolve_common_contents()` が、 `path` または `content` が未定義のページでエラーを起こす不具合を修正。

### px2-path-resolver 2.0.7 (2016年7月27日)

- `tomk79\pickles2\pathResolver\main::resolve_common_contents()` で、 `content` が 0バイト のファイルの場合に異常終了する不具合を修正。

### px2-path-resolver 2.0.6 (2016年5月25日)

- HTML 1枚 のファイルサイズが大きくてパースに失敗する場合のエラー処理を追加。


## ライセンス - License

MIT License


## 作者 - Author

- (C)Tomoya Koyanagi <tomk79@gmail.com>
- website: <http://www.pxt.jp/>
- Twitter: @tomk79 <http://twitter.com/tomk79/>
