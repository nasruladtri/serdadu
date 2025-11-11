# Script untuk menambahkan PHP ke PATH
# Jalankan dengan: . .\setup-php-path.ps1

$laragonPhpPath = "C:\laragon\bin\php"
if (Test-Path $laragonPhpPath) {
    $phpVersions = Get-ChildItem -Path $laragonPhpPath -Directory | Sort-Object Name -Descending
    if ($phpVersions.Count -gt 0) {
        $latestPhp = $phpVersions[0].FullName
        if ($env:Path -notlike "*$latestPhp*") {
            $env:Path = "$latestPhp;$env:Path"
            Write-Host "✓ PHP ditambahkan ke PATH: $latestPhp" -ForegroundColor Green
            Write-Host "✓ Versi PHP: $(php -v | Select-Object -First 1)" -ForegroundColor Green
        } else {
            Write-Host "✓ PHP sudah ada di PATH" -ForegroundColor Yellow
        }
    } else {
        Write-Host "✗ Tidak ada instalasi PHP ditemukan di Laragon" -ForegroundColor Red
    }
} else {
    Write-Host "✗ Path Laragon tidak ditemukan: $laragonPhpPath" -ForegroundColor Red
}

