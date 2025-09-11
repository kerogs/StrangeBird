<?php

require_once __DIR__ . '/includes/core.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StrangeBird | Home</title>

    <link rel="stylesheet" href="/assets/styles/css/style.css">

    <?php require_once __DIR__ . '/includes/head.php' ?>
</head>

<body class="dark">

    <?php require_once __DIR__ . '/includes/header.php' ?>

    <main>

        <div class="add-scan-page">
            <h2>Add New Scan</h2>

            <form id="scanForm" method="POST" action="/actions/add_scan_form.php" enctype="multipart/form-data">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" required>

                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4" required></textarea>

                <label for="tag">Tags (comma separated)</label>
                <input type="text" id="tag" name="tag" placeholder="Action,Adventure" required>

                <div class="image-upload-container">
                    <div class="drop-zone" data-type="cover">
                        <p>Drag & Drop Cover Image (JPG, PNG, WEBP)</p>
                        <input type="file" name="cover" accept="image/png, image/jpeg, image/webp" required>
                        <img class="preview" src="" alt="Cover Preview">
                    </div>

                    <div class="drop-zone" data-type="background">
                        <p>Drag & Drop Background Image (JPG, PNG, WEBP) (optional)</p>
                        <input type="file" name="background" accept="image/png, image/jpeg, image/webp">
                        <img class="preview" src="" alt="Background Preview">
                    </div>
                </div>

                <button type="submit" class="btn-submit">Add Scan</button>
            </form>
        </div>

        <script>
            document.querySelectorAll('.drop-zone').forEach(zone => {
                const input = zone.querySelector('input[type="file"]');
                const preview = zone.querySelector('.preview');

                zone.addEventListener('dragover', e => {
                    e.preventDefault();
                    zone.style.backgroundColor = '#ff6126aa';
                });

                zone.addEventListener('dragleave', e => {
                    e.preventDefault();
                    zone.style.backgroundColor = '';
                });

                zone.addEventListener('drop', e => {
                    e.preventDefault();
                    const file = e.dataTransfer.files[0];
                    if (!file) return;
                    input.files = e.dataTransfer.files;

                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = e => preview.src = e.target.result;
                        reader.readAsDataURL(file);
                    }
                });

                input.addEventListener('change', () => {
                    const file = input.files[0];
                    if (file && file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = e => preview.src = e.target.result;
                        reader.readAsDataURL(file);
                    }
                });
            });
        </script>


    </main>


</body>

</html>