$ErrorActionPreference = 'Stop'
$src  = Split-Path $PSScriptRoot -Parent
$dest = Join-Path (Split-Path $src -Parent) 'sms2_deploy_staging'
$zip  = Join-Path (Split-Path $src -Parent) 'sms2_deploy.zip'

if (Test-Path $dest) { Remove-Item $dest -Recurse -Force }
New-Item -ItemType Directory -Path $dest | Out-Null

robocopy $src $dest /E /XD .git .cursor /XF config\local.php .git-sync.log .git-sync.lock /NFL /NDL /NJH /NJS /nc /ns /np | Out-Null

Copy-Item (Join-Path $src '.htaccess.infinityfree') (Join-Path $dest '.htaccess') -Force

$localTemplate = Join-Path $dest 'config\local.infinityfree.example.php'
$localTarget   = Join-Path $dest 'config\local.php'
Copy-Item $localTemplate $localTarget -Force

if (Test-Path $zip) { Remove-Item $zip -Force }
Compress-Archive -Path (Join-Path $dest '*') -DestinationPath $zip

Write-Host "Created: $zip"
Write-Host "Next: edit config/local.php on server with MySQL password, then upload/extract zip in File Manager."
