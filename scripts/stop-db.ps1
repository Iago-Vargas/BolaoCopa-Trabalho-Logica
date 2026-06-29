$processes = Get-Process mysqld,mariadbd -ErrorAction SilentlyContinue

if (-not $processes) {
    Write-Host "MariaDB nao esta rodando."
    exit 0
}

$processes | Stop-Process
Write-Host "MariaDB parado."
