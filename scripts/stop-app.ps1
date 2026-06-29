$projectRoot = Resolve-Path (Join-Path $PSScriptRoot "..")
$artisan = Join-Path $projectRoot "artisan"

$phpProcesses = Get-CimInstance Win32_Process -Filter "Name = 'php.exe'" | Where-Object {
    $_.CommandLine -like "*$artisan*" -and (
        $_.CommandLine -like "*serve*" -or
        $_.CommandLine -like "*schedule:work*" -or
        $_.CommandLine -like "*queue:work*"
    )
}

if (-not $phpProcesses) {
    Write-Host "Laravel nao esta rodando."
    exit 0
}

$phpProcesses | ForEach-Object {
    Stop-Process -Id $_.ProcessId
}

Write-Host "Laravel, Scheduler e Queue parados."
