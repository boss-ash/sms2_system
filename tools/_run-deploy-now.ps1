$ErrorActionPreference = 'Stop'
$pass = 'HVfvZiN3gF8RfyR'
& "$PSScriptRoot\build-infinityfree-zip.ps1" -Password $pass
& "$PSScriptRoot\deploy-infinityfree.ps1" -FtpPass $pass
