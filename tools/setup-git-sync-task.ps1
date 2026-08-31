#Requires -Version 5.1
# Registers a Windows scheduled task to keep GitHub and local files in sync.
param(
    [switch]$Remove
)

$ErrorActionPreference = 'Stop'
$RepoRoot = (Resolve-Path (Join-Path $PSScriptRoot '..')).Path
$TaskName = 'SMS2 Git Auto-Sync'
$WatcherScript = Join-Path $RepoRoot 'tools\git-auto-sync.ps1'
$TaskAction = New-ScheduledTaskAction `
    -Execute 'powershell.exe' `
    -Argument "-NoProfile -ExecutionPolicy Bypass -WindowStyle Hidden -File `"$WatcherScript`" -Watch -PollIntervalSeconds 60" `
    -WorkingDirectory $RepoRoot
$TaskTrigger = New-ScheduledTaskTrigger -AtLogOn -User $env:USERNAME
$TaskSettings = New-ScheduledTaskSettingsSet `
    -AllowStartIfOnBatteries `
    -DontStopIfGoingOnBatteries `
    -StartWhenAvailable `
    -RestartCount 3 `
    -RestartInterval (New-TimeSpan -Minutes 1)

if ($Remove) {
    Unregister-ScheduledTask -TaskName $TaskName -Confirm:$false -ErrorAction SilentlyContinue
    Write-Host "Removed scheduled task: $TaskName"
    return
}

Register-ScheduledTask `
    -TaskName $TaskName `
    -Action $TaskAction `
    -Trigger $TaskTrigger `
    -Settings $TaskSettings `
    -Description 'Pulls GitHub updates and auto-commits local SMS2 changes.' `
    -Force | Out-Null

Write-Host "Registered scheduled task: $TaskName"
Write-Host 'It starts at Windows logon and keeps local files synced with GitHub.'
Write-Host "Manual start: tools\start-git-auto-sync.bat"
Write-Host "Remove task: powershell -File tools\setup-git-sync-task.ps1 -Remove"
