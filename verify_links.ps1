
$htmlFiles = Get-ChildItem -Path . -Filter *.html

foreach ($file in $htmlFiles) {
    $content = Get-Content $file.FullName
    $links = $content | Select-String -Pattern 'href="([^"]+)"|action="([^"]+)"' -AllMatches

    foreach ($match in $links.Matches) {
        $link = $match.Groups[1].Value
        if ([string]::IsNullOrEmpty($link)) { $link = $match.Groups[2].Value }

        if ($link -match "^(https?://|mailto:|tel:|#)") {
            continue
        }

        # Handle anchor links like page.html#section
        $cleanLink = $link -replace "#.*$", ""
        
        if ([string]::IsNullOrEmpty($cleanLink)) { continue }

        $targetPath = Join-Path $file.DirectoryName $cleanLink
        if (-not (Test-Path $targetPath)) {
            Write-Host "Broken link in $($file.Name): $link (Target not found: $targetPath)" -ForegroundColor Red
        }
    }
}
