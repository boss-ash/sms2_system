@echo off
title SMS2 Git Auto-Sync
cd /d "%~dp0.."
echo Starting git auto-sync watcher...
echo - Local file changes auto commit and push to GitHub
echo - GitHub updates are pulled every 60 seconds
echo Close this window to stop.
powershell -NoProfile -ExecutionPolicy Bypass -File "tools\git-auto-sync.ps1" -Watch -PollIntervalSeconds 60
pause
