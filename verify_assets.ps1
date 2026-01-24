$rootPath = Get-Location
Write-Host "Checking in: $rootPath"
$htmlFiles = Get-ChildItem -Path $rootPath -Filter "*.html"

$imgPattern = 'src=["''](.*?)["'']'
$cssPattern = 'url\(["'']?(.*?)["'']?\)'

$report = @()

foreach ($file in $htmlFiles) {
    $content = Get-Content $file.FullName -Raw -Encoding UTF8
    
    # Check src attributes
    $srcMatches = [regex]::Matches($content, $imgPattern)
    foreach ($match in $srcMatches) {
        $link = $match.Groups[1].Value
        
        if ($link -match "^(http|#|mailto:|tel:)") { continue }
        if ([string]::IsNullOrWhiteSpace($link)) { continue }
        
        $link = $link.Split('?')[0].Split('#')[0]
        
        $targetPath = ""
        if ($link.StartsWith("/")) {
             # Remove leading slash and combine with root
            $targetPath = Join-Path $rootPath $link.TrimStart("/")
        } else {
            $targetPath = Join-Path $file.DirectoryName $link
        }
        
        if (-not (Test-Path $targetPath)) {
            $report += "MISSING: $($file.Name) -> $link"
        }
    }
    
    # Check css url()
    $cssMatches = [regex]::Matches($content, $cssPattern)
    foreach ($match in $cssMatches) {
        $link = $match.Groups[1].Value
        
        if ($link -match "^(http|#|data:)") { continue }
        if ([string]::IsNullOrWhiteSpace($link)) { continue }
        
        $link = $link.Split('?')[0].Split('#')[0]
        
        $targetPath = ""
        if ($link.StartsWith("/")) {
            $targetPath = Join-Path $rootPath $link.TrimStart("/")
        } else {
            $targetPath = Join-Path $file.DirectoryName $link
        }
        
        if (-not (Test-Path $targetPath)) {
            $report += "MISSING: $($file.Name) -> $link (CSS)"
        }
    }
}

if ($report.Count -gt 0) {
    $report | Select-Object -Unique
} else {
    Write-Output "No missing assets found."
}
