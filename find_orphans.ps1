
# Valid path resolution by using current location context
$root = (Get-Location).Path

# 1. Get all files
$allFiles = Get-ChildItem -Path $root -Recurse -File -Include *.html,*.php,*.js,*.css,*.png,*.jpg,*.jpeg,*.gif,*.svg
if ($null -eq $allFiles) {
    Write-Host "Error: No files found. Check path."
    exit
}

$allFilesRel = $allFiles | ForEach-Object { $_.FullName.Substring($root.Length).Replace('\', '/').TrimStart('/') }

# 2. Files to ignore
$ignoreList = @(
    'audit_links.ps1', 'verify_links.py', 'find_orphans.py', 'find_orphans.ps1',
    'tailwind_config.js', 'package.json', 'package-lock.json', 'server.js', 
    '.htaccess', '404.html', 'sitemap.xml', 'robots.txt'
)
$ignoreDirs = @('.git', '.gemini', 'node_modules', 'payjp_test', 'api')

# 3. Find all references
$referenced = New-Object System.Collections.Generic.HashSet[string]
$referenced.Add("index.html") | Out-Null

$scanFiles = Get-ChildItem -Path $root -Recurse -File -Include *.html,*.php,*.js,*.css

foreach ($file in $scanFiles) {
    # Skip ignored dirs
    $skip = $false
    foreach ($dir in $ignoreDirs) { if ($file.FullName -match "\\$dir\\") { $skip = $true; break } }
    if ($skip) { continue }

    $content = Get-Content $file.FullName -Raw
    # Remove comments
    $content = [regex]::Replace($content, '<!--[\s\S]*?-->', '')
    
    # Match href, src, url
    $matches = [regex]::Matches($content, '(?:href|src|url)=["''\(]([^"''\)\?\#]+)')

    foreach ($m in $matches) {
        $link = $m.Groups[1].Value
        if ($link -match "^(http|#|mailto:|tel:|javascript:|data:)") { continue }
        if ([string]::IsNullOrWhiteSpace($link)) { continue }
        
        # Resolve to root-relative path
        $target = $null
        if ($link.StartsWith("/")) {
            $target = $link.TrimStart("/")
        } else {
             # Relative to file
             $dir = $file.DirectoryName.Substring($root.Length).Replace('\', '/').TrimStart('/')
             if ($dir -eq "") {
                 $target = $link
             } else {
                 $target = "$dir/$link"
             }
        }
        
        # Simple normalization
        $target = $target.Replace('./', '') 
        
        $referenced.Add($target) | Out-Null
    }
}

# 4. Compare
$orphans = @()
foreach ($f in $allFilesRel) {
    if ($f -in $ignoreList) { continue }
    
    $skip = $false
    foreach ($dir in $ignoreDirs) { if ($f -match "^$dir/") { $skip = $true; break } }
    if ($skip) { continue }

    # Check matches
    $isRef = $false
    foreach ($r in $referenced) {
        # Loose matching to account for path variations
        if ($f.EndsWith($r)) { $isRef = $true; break }
        if ($r.EndsWith($f)) { $isRef = $true; break }
    }
    
    if (-not $isRef) {
        $orphans += $f
    }
}

if ($orphans.Count -eq 0) {
    Write-Host "PASS: No orphaned files found."
} else {
    Write-Host "FAIL: Found $($orphans.Count) potential orphans:"
    $orphans | ForEach-Object { Write-Host " - $_" }
}
