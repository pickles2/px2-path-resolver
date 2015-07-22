# tomk79/px2-path-resolver


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
        <a href="https://travis-ci.org/tomk79/px2-path-resolver"><img src="https://secure.travis-ci.org/tomk79/px2-path-resolver.svg?branch=master"></a>
      </td>
      <td align="center">
        <a href="https://ci.appveyor.com/project/tomk79/px2-path-resolver"><img src="https://ci.appveyor.com/api/projects/status/9u7o6tf510e8r7e0/branch/master?svg=true"></a>
      </td>
    </tr>
    <tr>
      <th>develop</th>
      <td align="center">
        <a href="https://travis-ci.org/tomk79/px2-path-resolver"><img src="https://secure.travis-ci.org/tomk79/px2-path-resolver.svg?branch=develop"></a>
      </td>
      <td align="center">
        <a href="https://ci.appveyor.com/project/tomk79/px2-path-resolver"><img src="https://ci.appveyor.com/api/projects/status/9u7o6tf510e8r7e0/branch/develop?svg=true"></a>
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
        "tomk79/px2-path-resolver": "2.*"
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


## ライセンス - License

MIT License


## 作者 - Author

- (C)Tomoya Koyanagi <tomk79@gmail.com>
- website: <http://www.pxt.jp/>
- Twitter: @tomk79 <http://twitter.com/tomk79/>
