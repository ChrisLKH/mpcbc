@echo off
REM Sets up the MPCBC website on a brand-new computer. Double-click this file.
REM Everything it does lives in install.ps1, next to this file.
powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0install.ps1"
if errorlevel 1 pause
