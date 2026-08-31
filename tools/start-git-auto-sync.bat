@echo off
title SMS2 Git Auto-Sync
cd /d "%~dp0.."
echo Starting git auto-sync watcher...
echo Changes will auto commit, pull, and push to GitHub.
echo Close this window to stop.
powershell -NoProfile -ExecutionPolicy Bypass -File "tools\git-auto-sync.ps1" -Watch
pause
