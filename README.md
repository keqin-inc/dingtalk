# keqin/dingtalk 克勤 Laravel 钉钉扩展包

![PHP Composer](https://github.com/keqin-inc/dingtalk/workflows/PHP%20Composer/badge.svg)

## 安装
`composer require keqin/dingtalk`

安装完成后，使用 `php artisan vendor:publish --provider="Keqin\Dingtalk\ServiceProvider"` 自动生成 `config/dingtalk.php` 和 views: `resources/views/ddlogin.blade.php` 用于钉钉一键登录。

## AccessToken

### 自动更新
安装后自动注入 Artisan 命令 `dingtalk:accesstoken --sync` 即可自动更新。Token 会自动存入 Laravel Cache 中。

使用 `dingtalk:accesstoken --show` 查看最新的 Token 信息。

### 手动访问 Access Token

`\Dingtalk::accessToken()` 可访问到 Dingtalk AccessToken 对象，支持转成字符串，为当前 AccessToken。

## 调用钉钉接口

Dingtalk 支持 get 和 post 接口，url 中的 access_token 可以省略，库会自动补上。

- `Dingtalk::get($url, $queryString = [])`
- `Dingtalk::post($url, $body)`

返回了 \Http::get 和 \Http::post 的对象。响应对象的[具体用法参见 Laravel 文档](https://laravel.com/docs/7.x/http-client#making-requests)。

### 用法举例

```php
$list = \Dingtalk::get('topapi/role/list')->json();
```

### 错误处理
默认情况下 errcode 不为 0，将抛出 Exception。如果希望直接返回，可以

```php
\Dingtalk::forceReturn()
    ->get('topapi/role/list')
    ->json()
```
