$ErrorActionPreference = 'Stop'

$projectRoot = Split-Path -Parent $PSScriptRoot
$temporaryIniDirectory = Join-Path ([System.IO.Path]::GetTempPath()) ("perpus-api-sqlite-" + [guid]::NewGuid().ToString('N'))
$previousScanDirectory = $env:PHP_INI_SCAN_DIR

try {
    $phpInfo = & php -i
    if ($LASTEXITCODE -ne 0) {
        throw 'Unable to read PHP configuration.'
    }

    $extensionLine = $phpInfo | Where-Object { $_ -match '^extension_dir => ' } | Select-Object -First 1
    if ($extensionLine -notmatch '^extension_dir => (.+?) =>') {
        throw 'Unable to determine the PHP extension directory.'
    }
    $extensionDirectory = $Matches[1]

    New-Item -ItemType Directory -Path $temporaryIniDirectory | Out-Null
    $extensionDirectory = $extensionDirectory.Replace('\', '/')
    @"
extension_dir="$extensionDirectory"
extension=pdo_sqlite
extension=sqlite3
"@ | Set-Content -Path (Join-Path $temporaryIniDirectory 'sqlite.ini') -Encoding ASCII

    $env:PHP_INI_SCAN_DIR = $temporaryIniDirectory
    Set-Location $projectRoot
    & php artisan test
    exit $LASTEXITCODE
}
finally {
    if ($null -eq $previousScanDirectory) {
        Remove-Item Env:PHP_INI_SCAN_DIR -ErrorAction SilentlyContinue
    } else {
        $env:PHP_INI_SCAN_DIR = $previousScanDirectory
    }

    if (Test-Path -LiteralPath $temporaryIniDirectory) {
        Remove-Item -LiteralPath $temporaryIniDirectory -Recurse -Force
    }
}
