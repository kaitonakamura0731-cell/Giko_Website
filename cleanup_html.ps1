$rootPath = Get-Location
$htmlFiles = Get-ChildItem -Path $rootPath -Filter "*.html"

foreach ($file in $htmlFiles) {
    $content = Get-Content $file.FullName -Raw -Encoding UTF8
    $originalContent = $content

    # 1. Inject tailwind_config.js and remove inline config
    # Pattern to find tailwind CDN and the following inline config
    $tailwindPattern = '(<script src="https://cdn.tailwindcss.com"></script>)(\s*)<script>\s*tailwind\.config\s*=\s*\{[\s\S]*?\}\s*</script>'
    
    if ($content -match $tailwindPattern) {
        $content = $content -replace $tailwindPattern, '$1$2<script src="./tailwind_config.js"></script>'
    } elseif ($content -match '<script src="https://cdn.tailwindcss.com"></script>') {
        # If CDN exists but no inline config (or unmatched), ensure config is added
        if (-not ($content -match 'src="./tailwind_config.js"')) {
            $content = $content -replace '(<script src="https://cdn.tailwindcss.com"></script>)', '$1<script src="./tailwind_config.js"></script>'
        }
    }

    # 2. Remove specific inline scripts that were moved to script.js
    
    # Scroll to Top / Mobile Menu / Filter mixed block (often in index.html)
    $scriptBlockPattern1 = '<script>\s*// Scroll to Top Logic[\s\S]*?// Filter functionality[\s\S]*?// Mobile Menu[\s\S]*?</script>'
    $content = $content -replace $scriptBlockPattern1, ''
    
    # Simple Mobile Menu block (often in subpages)
    $scriptBlockPattern2 = '<script>\s*const mobileMenuBtn = document\.getElementById\(''mobile-menu-btn''\);[\s\S]*?</script>'
    $content = $content -replace $scriptBlockPattern2, ''

    # Just Scroll to Top logic if isolated
    $scriptBlockPattern3 = '<script>\s*// Scroll to Top Logic[\s\S]*?</script>'
    # Be careful not to remove if it was already part of block1. 
    # Since we replaced block1 already, this handles isolated cases.
    $content = $content -replace $scriptBlockPattern3, ''

    # Clean up empty lines created by removal
    # (Optional, but good for cleanliness. Regex handles spacing reasonably well)

    if ($content -ne $originalContent) {
        Write-Host "Updating $($file.Name)"
        $content | Set-Content $file.FullName -Encoding UTF8
    }
}
