
$files = Get-ChildItem -Recurse -Include *.html,*.php

$errors = @()

foreach ($file in $files) {
    $content = Get-Content $file.FullName -Raw
    
    # Remove HTML comments to avoid false positives
    $content = [regex]::Replace($content, '<!--[\s\S]*?-->', '')

    $links = [regex]::Matches($content, '(?:href|src)=["'']([^"''#?]+)[^"'']*["'']')

    foreach ($match in $links) {
        $link = $match.Groups[1].Value
        
        if ($link -match "^(http|https|mailto:|tel:|#|javascript:)") { continue }
        if ([string]::IsNullOrWhiteSpace($link)) { continue }
        # Ignore JS template literals
        if ($link -match "\$\{.*\}") { continue }

        # Resolve path relative to the file
        $targetPath = $null
        if ($link.StartsWith("/")) {
            $targetPath = Join-Path (Get-Location) $link.TrimStart("/")
        } else {
            $targetPath = Join-Path $file.DirectoryName $link
        }

        # Check existence
        if (-not (Test-Path $targetPath)) {
            if (-not ((Test-Path "$targetPath\index.html") -or (Test-Path "$targetPath\index.php"))) {
                 $relFile = $file.FullName.Substring((Get-Location).Path.Length)
                 $errors += "[$relFile] Broken: '$link'"
            }
        }
    }
}

if ($errors.Count -eq 0) {
    Write-Host "PASS: All checks passed."
} else {
    Write-Host "FAIL: Found $($errors.Count) errors:"
    $errors | ForEach-Object { Write-Host $_ }
}
