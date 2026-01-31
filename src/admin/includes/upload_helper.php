<?php
// helpers/upload.php

function handleUpload($fileInputName, $targetDir = '../assets/images/uploads/')
{
    // Ensure target dir exists
    // (In PHP, mkdir here causes permissions complexity with local server user, 
    // assuming it exists or running `mkdir` command via tools is safer. 
    // I did run `mkdir` previously.)

    if (isset($_FILES[$fileInputName]) && $_FILES[$fileInputName]['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES[$fileInputName]['tmp_name'];
        $name = basename($_FILES[$fileInputName]['name']);

        // Sanitize filename to prevent issues (simple timestamp prefix)
        $ext = pathinfo($name, PATHINFO_EXTENSION);
        $newName = time() . '_' . uniqid() . '.' . $ext;

        // Determine relative path for saving in DB
        // If targetDir includes '../', remove it for DB storage if we want absolute-like paths, 
        // OR keep relative to the PHP file executing?
        // Usually, consistent relative paths from webroot are best.
        // Let's assume we store relative to the `src` root if usage allows, OR `../assets/...` logic.
        // If strict relative to admin file: `../assets/images/uploads/filename`

        $targetPath = $targetDir . $newName;

        // Move file (Assuming PHP script is in /admin/works/ or /admin/store/)
        // We might need to adjust based on current working directory.
        // admin/.../edit.php -> ../../../assets/images/uploads/ ? No.
        // edit.php is in admin/works/, so `../` puts us in admin/, `../../` puts us in src/
        // My mkdir command was: `src/assets/images/uploads`
        // So from `src/admin/works/edit.php`, it is `../../assets/images/uploads/`

        // Let's adjust targetDir logic to be passed correctly by caller.

        if (move_uploaded_file($tmpName, $targetPath)) {
            return $targetPath;
        }
    }
    return null;
}
