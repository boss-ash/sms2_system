# Deploy SMS2 to InfinityFree via FTP (WinSCP or .NET WebClient)
# Usage: set SMS2_FTP_PASS=your_hosting_password then run this script
param(
    [string]$FtpPass = $env:SMS2_FTP_PASS,
    [string]$ZipPath = 'C:\xampp\htdocs\sms2_deploy.zip',
    [string]$Staging = 'C:\xampp\htdocs\sms2_deploy_staging'
)

$ErrorActionPreference = 'Stop'

if (-not $FtpPass) {
    Write-Error 'Set SMS2_FTP_PASS environment variable to your InfinityFree hosting account password.'
}

$ftpHost = 'ftpupload.net'
$ftpUser = 'if0_42794375'
$remoteDir = '/htdocs'

# Ensure zip exists
if (-not (Test-Path $ZipPath)) {
    & "$PSScriptRoot\build-infinityfree-zip.ps1"
}

# Extract locally for upload
if (Test-Path $Staging) { Remove-Item $Staging -Recurse -Force }
Expand-Archive -Path $ZipPath -DestinationPath $Staging -Force

function Upload-FtpFile($localPath, $remotePath) {
    $uri = "ftp://${ftpHost}${remotePath}"
    $request = [System.Net.FtpWebRequest]::Create($uri)
    $request.Method = [System.Net.WebRequestMethods+Ftp]::UploadFile
    $request.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $FtpPass)
    $request.UseBinary = $true
    $request.UsePassive = $true
    $bytes = [System.IO.File]::ReadAllBytes($localPath)
    $request.ContentLength = $bytes.Length
    $stream = $request.GetRequestStream()
    $stream.Write($bytes, 0, $bytes.Length)
    $stream.Close()
    $response = $request.GetResponse()
    $response.Close()
}

function Ensure-FtpDir($remotePath) {
    try {
        $uri = "ftp://${ftpHost}${remotePath}"
        $request = [System.Net.FtpWebRequest]::Create($uri)
        $request.Method = [System.Net.WebRequestMethods+Ftp]::MakeDirectory
        $request.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $FtpPass)
        $request.UsePassive = $true
        $response = $request.GetResponse()
        $response.Close()
    } catch { }
}

function Upload-FtpTree($localDir, $remoteDir) {
    Ensure-FtpDir $remoteDir
    Get-ChildItem -Path $localDir -Force | ForEach-Object {
        $remote = ($remoteDir.TrimEnd('/') + '/' + $_.Name)
        if ($_.PSIsContainer) {
            Upload-FtpTree $_.FullName $remote
        } else {
            Write-Host "Uploading $remote"
            Upload-FtpFile $_.FullName $remote
        }
    }
}

Write-Host "Uploading to ftp://${ftpHost}${remoteDir} ..."
Upload-FtpTree $Staging $remoteDir
Write-Host 'Upload complete.'
Write-Host 'Next: open https://bestlinksms2portal.free.nf/setup/deploy-db.php?token=bcp-sms2-deploy-2026'
