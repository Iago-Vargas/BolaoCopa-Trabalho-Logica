$php = "$env:LOCALAPPDATA\Microsoft\WinGet\Packages\PHP.PHP.8.4_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe"
$projectRoot = Resolve-Path (Join-Path $PSScriptRoot "..")
$artisan = Join-Path $projectRoot "artisan"
$logs = Join-Path $projectRoot "storage\logs"

if (-not (Test-Path $php)) {
    $php = "php"
}

Set-Location $projectRoot

& (Join-Path $PSScriptRoot "start-db.ps1")

function Start-ArtisanProcess {
    param (
        [string] $Name,
        [string] $Arguments,
        [string] $OutputLog,
        [string] $ErrorLog
    )

    $running = Get-CimInstance Win32_Process -Filter "Name = 'php.exe'" | Where-Object {
        $_.CommandLine -like "*$artisan*" -and $_.CommandLine -like "*$Arguments*"
    }

    if ($running) {
        Write-Host "$Name ja esta rodando."
        return
    }

    Start-Process `
        -FilePath $php `
        -ArgumentList "`"$artisan`" $Arguments" `
        -WorkingDirectory $projectRoot `
        -WindowStyle Hidden `
        -RedirectStandardOutput (Join-Path $logs $OutputLog) `
        -RedirectStandardError (Join-Path $logs $ErrorLog)

    Write-Host "$Name iniciado."
}

Start-ArtisanProcess `
    -Name "Laravel Scheduler" `
    -Arguments "schedule:work" `
    -OutputLog "schedule-work.log" `
    -ErrorLog "schedule-work-error.log"

Start-ArtisanProcess `
    -Name "Laravel Queue" `
    -Arguments "queue:work --sleep=5 --tries=3 --timeout=90" `
    -OutputLog "queue-work.log" `
    -ErrorLog "queue-work-error.log"

& $php $artisan serve --host=127.0.0.1 --port=8000
