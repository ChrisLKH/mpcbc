@echo off
REM Opens the MPCBC website control panel. Double-click this file.
REM
REM Two things about this file are deliberate:
REM
REM 1. It does NOT pass -WindowStyle Hidden. That switch sets the whole
REM    process's STARTUPINFO to SW_HIDE, and Windows applies that to the
REM    first top-level window the process shows - which is the control
REM    panel itself. The app would start and display nothing at all.
REM    control-panel.ps1 hides its own console instead, once it owns it.
REM
REM 2. It has no logic, and should stay that way. cmd.exe reads a .bat
REM    incrementally from a byte offset, so if "Get the Latest Version"
REM    ever rewrites this file while it is running, execution can resume
REM    mid-line. The fix is to give it no reason to ever change: the real
REM    work lives in the .ps1 files, which PowerShell reads into memory
REM    whole before running.
start "" powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0app\control-panel.ps1"
