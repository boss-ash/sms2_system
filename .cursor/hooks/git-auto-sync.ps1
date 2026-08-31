# Cursor hook: queue git auto-sync after file edits.
$inputJson = [Console]::In.ReadToEnd() | ConvertFrom-Json -ErrorAction SilentlyContinue
$repoRoot = Split-Path (Split-Path $PSScriptRoot -Parent) -Parent
$syncScript = Join-Path $repoRoot 'tools\git-auto-sync.ps1'

if (Test-Path $syncScript) {
    Start-Process -FilePath 'powershell.exe' `
        -ArgumentList @('-NoProfile', '-ExecutionPolicy', 'Bypass', '-File', $syncScript) `
        -WindowStyle Hidden `
        -WorkingDirectory $repoRoot | Out-Null
}

Write-Output '{}'
