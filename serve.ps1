$php = (Get-Command php -ErrorAction Stop).Source
$arguments = @('-S', 'localhost:2000', '-t', 'public')

$sqliteEnabled = php -r "echo extension_loaded('pdo_sqlite') ? 'yes' : 'no';"
if ($sqliteEnabled -ne 'yes') {
    $extensionDirectory = Join-Path (Split-Path $php) 'ext'
    $sqliteExtension = Join-Path $extensionDirectory 'php_pdo_sqlite.dll'
    if (-not (Test-Path -LiteralPath $sqliteExtension)) {
        throw 'PHP PDO SQLite chưa được cài đặt.'
    }
    $arguments = @(
        '-d', "extension_dir=$extensionDirectory",
        '-d', 'extension=pdo_sqlite'
    ) + $arguments
}

& $php @arguments
