param(
    [string]$Password = $env:SMS2_FTP_PASS
)

$ErrorActionPreference = 'Stop'
$src  = Split-Path $PSScriptRoot -Parent
$dest = Join-Path (Split-Path $src -Parent) 'sms2_deploy_staging'
$zip  = Join-Path (Split-Path $src -Parent) 'sms2_deploy.zip'

if (Test-Path $dest) { Remove-Item $dest -Recurse -Force }
New-Item -ItemType Directory -Path $dest | Out-Null

robocopy $src $dest /E /XD .git .cursor /XF config\local.php .git-sync.log .git-sync.lock /NFL /NDL /NJH /NJS /nc /ns /np | Out-Null

Copy-Item (Join-Path $src '.htaccess.infinityfree') (Join-Path $dest '.htaccess') -Force

$localTarget = Join-Path $dest 'config\local.php'
Copy-Item (Join-Path $src 'config\local.infinityfree.example.php') $localTarget -Force

if ($Password) {
    (Get-Content $localTarget -Raw).Replace('your_hosting_account_password', $Password) | Set-Content $localTarget -NoNewline
}

if (Test-Path $zip) { Remove-Item $zip -Force }
Compress-Archive -Path (Join-Path $dest '*') -DestinationPath $zip

Write-Host "Created: $zip"
