$mysqlData = "C:\Program Files\MariaDB 12.3\data\my.ini"
$mysqld = "C:\Program Files\MariaDB 12.3\bin\mysqld.exe"
$port = 3306

if (-not (Test-Path $mysqld)) {
    $mysqld = "C:\Program Files\MariaDB 12.3\bin\mariadbd.exe"
}

if (-not (Test-Path $mysqld)) {
    throw "MariaDB nao encontrado em C:\Program Files\MariaDB 12.3\bin"
}

$listener = Get-NetTCPConnection -LocalPort $port -State Listen -ErrorAction SilentlyContinue | Select-Object -First 1

if ($listener) {
    $process = Get-Process -Id $listener.OwningProcess -ErrorAction SilentlyContinue

    if ($process -and $process.ProcessName -in @("mysqld", "mariadbd")) {
        Write-Host "MariaDB ja esta rodando em 127.0.0.1:$port."
        exit 0
    }

    $processName = if ($process) { $process.ProcessName } else { "PID $($listener.OwningProcess)" }
    throw "A porta $port ja esta em uso por $processName. Pare esse processo antes de iniciar o MariaDB."
}

$running = Get-Process mysqld,mariadbd -ErrorAction SilentlyContinue
if ($running) {
    throw "MariaDB esta rodando, mas nao esta escutando na porta $port. Pare o processo atual e inicie novamente por este script."
}

Start-Process -FilePath $mysqld -ArgumentList "--defaults-file=`"$mysqlData`"" -WindowStyle Hidden
Start-Sleep -Seconds 3

$listener = Get-NetTCPConnection -LocalPort $port -State Listen -ErrorAction SilentlyContinue | Select-Object -First 1
if (-not $listener) {
    throw "MariaDB foi iniciado, mas a porta $port nao abriu. Verifique C:\Program Files\MariaDB 12.3\data\IAGO.err."
}

Write-Host "MariaDB iniciado em 127.0.0.1:$port."
