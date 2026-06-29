$serviceName = "MariaDB"
$mysqlData = "C:\Program Files\MariaDB 12.3\data\my.ini"
$mysqld = "C:\Program Files\MariaDB 12.3\bin\mysqld.exe"

if (-not (Test-Path $mysqld)) {
    $mysqld = "C:\Program Files\MariaDB 12.3\bin\mariadbd.exe"
}

if (-not (Test-Path $mysqld)) {
    throw "MariaDB nao encontrado em C:\Program Files\MariaDB 12.3\bin"
}

if (-not (Test-Path $mysqlData)) {
    throw "Arquivo de configuracao nao encontrado: $mysqlData"
}

$isAdmin = ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).
    IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)

if (-not $isAdmin) {
    Start-Process `
        -FilePath "powershell.exe" `
        -ArgumentList "-NoProfile -ExecutionPolicy Bypass -File `"$PSCommandPath`"" `
        -Verb RunAs

    Write-Host "Solicitacao de Administrador aberta. Confirme o UAC para registrar o servico."
    exit 0
}

$existing = Get-Service -Name $serviceName -ErrorAction SilentlyContinue

if (-not $existing) {
    & $mysqld --install-manual $serviceName

    if ($LASTEXITCODE -ne 0) {
        throw "Falha ao registrar o servico $serviceName."
    }
}

Set-Service -Name $serviceName -StartupType Manual
sc.exe description $serviceName "MariaDB do projeto BolaoCopa na porta 3306. Inicializacao manual." | Out-Null

Write-Host "Servico $serviceName registrado com inicializacao Manual."
Write-Host "Para iniciar: Start-Service $serviceName"
Write-Host "Para parar:   Stop-Service $serviceName"
