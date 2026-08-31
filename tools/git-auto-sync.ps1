#Requires -Version 5.1
param(
    [switch]$Watch,
    [int]$DebounceSeconds = 8
)

$ErrorActionPreference = 'Stop'
$RepoRoot = (Resolve-Path (Join-Path $PSScriptRoot '..')).Path
$LockFile = Join-Path $RepoRoot '.git-sync.lock'
$LogFile = Join-Path $RepoRoot '.git-sync.log'

$IgnorePatterns = @(
    '\.git\\',
    '\\storage\\uploads\\',
    '\\storage\\backups\\',
    '\\storage\\keys\\',
    '\\sms2_system\\',
    '\.git-sync\.'
)

function Write-SyncLog {
    param([string]$Message)
    $line = "[{0}] {1}" -f (Get-Date -Format 'yyyy-MM-dd HH:mm:ss'), $Message
    Add-Content -Path $LogFile -Value $line -Encoding UTF8
}

function Test-ShouldIgnorePath {
    param([string]$Path)
    foreach ($pattern in $IgnorePatterns) {
        if ($Path -match $pattern) {
            return $true
        }
    }
    return $false
}

function Invoke-GitCommand {
    param(
        [string[]]$Arguments
    )
    $previous = $ErrorActionPreference
    $ErrorActionPreference = 'Continue'
    try {
        $output = & git @Arguments 2>&1
        $code = [int]$LASTEXITCODE
        foreach ($line in @($output)) {
            if ($null -ne $line -and "$line".Trim() -ne '') {
                [void](Write-SyncLog "$line")
            }
        }
    }
    finally {
        $ErrorActionPreference = $previous
    }
    return $code
}

function Invoke-GitSync {
    $previousPreference = $ErrorActionPreference
    $ErrorActionPreference = 'Continue'

    if (Test-Path $LockFile) {
        $lockAge = (Get-Date) - (Get-Item $LockFile).LastWriteTime
        if ($lockAge.TotalMinutes -lt 5) {
            return
        }
        Remove-Item $LockFile -Force -ErrorAction SilentlyContinue
    }

    New-Item -ItemType File -Path $LockFile -Force | Out-Null

    try {
        Set-Location $RepoRoot

        $env:Path = [System.Environment]::GetEnvironmentVariable('Path', 'Machine') + ';' +
                    [System.Environment]::GetEnvironmentVariable('Path', 'User')

        git rev-parse --is-inside-work-tree 2>$null | Out-Null
        if ($LASTEXITCODE -ne 0) {
            Write-SyncLog 'Skipped: not a git repository.'
            return
        }

        $branch = (git rev-parse --abbrev-ref HEAD).Trim()
        if ($branch -eq 'HEAD') {
            Write-SyncLog 'Skipped: detached HEAD.'
            return
        }

        Write-SyncLog "Sync started on branch $branch"

        Invoke-GitCommand @('fetch', 'origin', $branch) | Out-Null
        $localHash = (git rev-parse HEAD).Trim()
        $remoteRef = "origin/$branch"
        git rev-parse --verify $remoteRef 2>$null | Out-Null
        if ($LASTEXITCODE -eq 0) {
            $remoteHash = (git rev-parse $remoteRef).Trim()
            if ($localHash -ne $remoteHash) {
                $mergeBase = (git merge-base HEAD $remoteRef).Trim()
                if ($mergeBase -eq $remoteHash) {
                    $pullCode = Invoke-GitCommand @('pull', '--rebase', 'origin', $branch)
                    if ($pullCode -ne 0) { return }
                }
                elseif ($mergeBase -eq $localHash) {
                    $pullCode = Invoke-GitCommand @('pull', '--ff-only', 'origin', $branch)
                    if ($pullCode -ne 0) { return }
                }
                else {
                    Write-SyncLog 'Pull skipped: local and remote diverged. Resolve manually.'
                    return
                }
            }
        }

        git add -A
        $status = git status --porcelain
        if ([string]::IsNullOrWhiteSpace($status)) {
            Write-SyncLog 'No local changes to commit.'
            return
        }

        $gitName = (git config --get user.name 2>$null)
        if ([string]::IsNullOrWhiteSpace($gitName)) {
            $gitName = $env:SMS2_GIT_NAME
        }
        if ([string]::IsNullOrWhiteSpace($gitName)) {
            $gitName = 'SMS2 Developer'
        }

        $gitEmail = (git config --get user.email 2>$null)
        if ([string]::IsNullOrWhiteSpace($gitEmail)) {
            $gitEmail = $env:SMS2_GIT_EMAIL
        }
        if ([string]::IsNullOrWhiteSpace($gitEmail)) {
            $gitEmail = 'sms2-dev@local'
        }

        $stamp = Get-Date -Format 'yyyy-MM-dd HH:mm:ss'
        $commitCode = Invoke-GitCommand @(
            '-c', "user.name=$gitName",
            '-c', "user.email=$gitEmail",
            'commit', '-m', "Auto-sync: $stamp"
        )
        if ($commitCode -ne 0) {
            Write-SyncLog 'Commit failed.'
            return
        }

        $pushCode = Invoke-GitCommand @('push', 'origin', $branch)
        if ($pushCode -eq 0) {
            Write-SyncLog 'Push completed.'
        }
        else {
            Write-SyncLog 'Push failed.'
        }
    }
    catch {
        Write-SyncLog ("Error: " + $_.Exception.Message)
    }
    finally {
        if (Test-Path $LockFile) {
            Remove-Item $LockFile -Force -ErrorAction SilentlyContinue
        }
        $ErrorActionPreference = $previousPreference
    }
}

if ($Watch) {
    Write-SyncLog 'File watcher started.'
    Write-Host "Watching $RepoRoot for changes..."
    Write-Host "Log: $LogFile"
    Write-Host 'Press Ctrl+C to stop.'

    $script:lastChange = $null
    $script:syncQueued = $false

    $watcher = New-Object System.IO.FileSystemWatcher
    $watcher.Path = $RepoRoot
    $watcher.IncludeSubdirectories = $true
    $watcher.EnableRaisingEvents = $true
    $watcher.NotifyFilter = [IO.NotifyFilters]'FileName, LastWrite, CreationTime, Size'

    $handler = {
        $path = $Event.SourceEventArgs.FullPath
        $patterns = @('\.git\\', '\\storage\\uploads\\', '\\storage\\backups\\', '\\storage\\keys\\', '\\sms2_system\\', '\.git-sync\.')
        foreach ($pattern in $patterns) {
            if ($path -match $pattern) { return }
        }
        $script:lastChange = Get-Date
        $script:syncQueued = $true
    }

    Register-ObjectEvent -InputObject $watcher -EventName Changed -SourceIdentifier 'Sms2GitChanged' -Action $handler | Out-Null
    Register-ObjectEvent -InputObject $watcher -EventName Created -SourceIdentifier 'Sms2GitCreated' -Action $handler | Out-Null
    Register-ObjectEvent -InputObject $watcher -EventName Deleted -SourceIdentifier 'Sms2GitDeleted' -Action $handler | Out-Null
    Register-ObjectEvent -InputObject $watcher -EventName Renamed -SourceIdentifier 'Sms2GitRenamed' -Action $handler | Out-Null

    try {
        while ($true) {
            Start-Sleep -Seconds 1
            if ($script:syncQueued -and $null -ne $script:lastChange) {
                $elapsed = ((Get-Date) - $script:lastChange).TotalSeconds
                if ($elapsed -ge $DebounceSeconds) {
                    $script:syncQueued = $false
                    Invoke-GitSync
                }
            }
        }
    }
    finally {
        $watcher.EnableRaisingEvents = $false
        $watcher.Dispose()
        Get-EventSubscriber | Where-Object { $_.SourceIdentifier -like 'Sms2Git*' } | Unregister-Event
    }
}
else {
    Start-Sleep -Seconds $DebounceSeconds
    Invoke-GitSync
}
