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


## 使い方 - Usage

```php

  // funcs: Before output
  $conf->funcs->before_output = [
    // 相対パス・絶対パスを変換して出力する
    'tomk79\pickles2\pathResolver\main::exec('.json_encode(array(
      'to' => 'relate',
      'supply_index_filename' => true
    )).')' ,
      // options
      //   string 'to':
      //     - relate: 相対パスへ変換
      //     - absolute: 絶対パスへ変換
      //     - pass: 変換を行わない(default)
      //   bool 'supply_index_filename':
      //     - true: 省略されたindexファイル名を補う
      //     - false: 補わない (default)

  ];
```


## ライセンス - License

MIT License


## 作者 - Author

- (C)Tomoya Koyanagi <tomk79@gmail.com>
- website: <http://www.pxt.jp/>
- Twitter: @tomk79 <http://twitter.com/tomk79/>


