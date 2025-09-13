<?php
require_once __DIR__ . '/includes/core.php';

// if not logged in, redirect to login page
if (!$auth->isLoggedIn()) {
    header('Location: /login');
    exit;
}

$scanId = $_GET['id'] ?? null;
if (!$scanId) {
    die("Scan ID missing.");
}

// TODO : security, check if the user id is the id_author
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StrangeBird | Add chapters</title>

    <link rel="stylesheet" href="/assets/styles/css/style.css">

    <?php require_once __DIR__ . '/includes/head.php' ?>
</head>

<body class="dark">

    <?php require_once __DIR__ . '/includes/header.php' ?>

    <div class="chapters-add">
        <h2>Add a chapter</h2>

        <form id="chapterForm" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id_scan" value="<?= htmlspecialchars($scanId) ?>">

            <label for="chapterNumber">Chapter number *</label>
            <input type="number" name="number" id="chapterNumber" required min="1">

            <label for="chapterName">chapter name (optional)</label>
            <input type="text" name="name" id="chapterName">

            <label>Images *</label>
            <div id="dropArea" class="drop-area">
                <p>Drag and drop here </p>
                <input type="file" id="fileInput" multiple accept=".jpg,.jpeg,.png,.webp">
            </div>

            <div id="preview" class="preview"></div>

            <button type="submit" class="btn-submit">Add chapter</button>
        </form>
    </div>

    <script>
        const dropArea = document.getElementById("dropArea");
        const fileInput = document.getElementById("fileInput");
        const preview = document.getElementById("preview");
        let filesToUpload = [];

        // Drag & Drop
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eName => dropArea.addEventListener(eName, preventDefaults, false));

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        ['dragenter', 'dragover'].forEach(eName => dropArea.addEventListener(eName, () => dropArea.classList.add('dragover')));
        ['dragleave', 'drop'].forEach(eName => dropArea.addEventListener(eName, () => dropArea.classList.remove('dragover')));
        dropArea.addEventListener('drop', e => handleFiles(e.dataTransfer.files));
        dropArea.addEventListener('click', () => fileInput.click());
        fileInput.addEventListener('change', () => handleFiles(fileInput.files));

        function handleFiles(fileList) {
            filesToUpload = Array.from(fileList);
            preview.innerHTML = '';
            filesToUpload.forEach((file, idx) => {
                const div = document.createElement('div');
                div.className = 'preview-item';
                div.textContent = `${idx+1}. ${file.name}`;
                preview.appendChild(div);
            });
        }

        // Upload par paquets
        document.getElementById('chapterForm').addEventListener('submit', async e => {
            e.preventDefault();
            if (filesToUpload.length === 0) {
                alert("Veuillez ajouter au moins une image.");
                return;
            }

            const idScan = document.querySelector('input[name="id_scan"]').value;
            const number = document.getElementById('chapterNumber').value;
            const name = document.getElementById('chapterName').value;

            // Crée d'abord l'entrée chapitre en BDD pour récupérer l'ID
            const createChapterRes = await fetch('/actions/form_add_chapters.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id_scan: idScan,
                    number,
                    name,
                    createOnly: true
                })
            });
            const {
                chapterId
            } = await createChapterRes.json();
            if (!chapterId) {
                alert("Erreur création chapitre");
                return;
            }

            // Upload par lots
            const chunkSize = 10; // nombre d'images par requête
            let storedNames = [];
            for (let i = 0; i < filesToUpload.length; i += chunkSize) {
                const chunk = filesToUpload.slice(i, i + chunkSize);
                const formData = new FormData();
                chunk.forEach(file => formData.append('images[]', file));
                formData.append('chapter_id', chapterId);
                formData.append('id_scan', idScan);

                const res = await fetch('/actions/form_add_chapters.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();
                if (data.success) storedNames.push(...data.files);
                else {
                    alert("Erreur upload images");
                    return;
                }
            }

            // Redirection
            window.location.href = `/scan/${idScan}`;
        });
    </script>

    <div class="chapters-add">
        <h2>or</h2>
        <form method="POST" action="/actions/form_add_chapters_zip.php" enctype="multipart/form-data">
            <input type="hidden" name="id_scan" value="<?= htmlspecialchars($scanId) ?>">

            <label>Upload a ZIP with all chapters and images *</label>
            <input type="file" name="chapters_zip" accept=".zip" required>

            <button type="submit" class="btn-submit">Upload all chapters</button>
        </form>
    </div>
    <!-- 

    <script>
        const foldersInput = document.getElementById('foldersInput');
        const preview = document.getElementById('previewChapters');

        let chapterFiles = {}; // { folderName: [File, ...] }

        foldersInput.addEventListener('change', e => {
            chapterFiles = {};
            preview.innerHTML = '';
            Array.from(foldersInput.files).forEach(file => {
                const pathParts = file.webkitRelativePath.split('/');
                const folderName = pathParts[0]; // dossier = chapitre
                if (!chapterFiles[folderName]) chapterFiles[folderName] = [];
                chapterFiles[folderName].push(file);
            });

            // Affichage
            Object.keys(chapterFiles).forEach(folderName => {
                const div = document.createElement('div');
                div.className = 'preview-item';
                div.textContent = `${folderName} (${chapterFiles[folderName].length} images)`;
                preview.appendChild(div);
            });
        });

        document.getElementById('multiChapterForm').addEventListener('submit', async e => {
            e.preventDefault();
            const idScan = document.querySelector('input[name="id_scan"]').value;

            // Parcours des dossiers (chapitres)
            for (const folderName in chapterFiles) {
                const files = chapterFiles[folderName];

                // Détection du numéro au début
                const numMatch = folderName.match(/^0*(\d+)/);
                const chapterNumber = numMatch ? parseInt(numMatch[1], 10) : 0;
                const chapterName = folderName; // nom complet du dossier

                // Crée l'entrée chapitre
                const createRes = await fetch('/actions/form_add_chapters.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id_scan: idScan,
                        number: chapterNumber,
                        name: chapterName,
                        createOnly: true
                    })
                });
                const {
                    chapterId
                } = await createRes.json();

                // Upload par lots d'images pour ce chapitre
                const chunkSize = 10;
                let storedNames = [];
                for (let i = 0; i < files.length; i += chunkSize) {
                    const chunk = files.slice(i, i + chunkSize);
                    const formData = new FormData();
                    chunk.forEach(file => formData.append('images[]', file));
                    formData.append('chapter_id', chapterId);
                    formData.append('id_scan', idScan);

                    const res = await fetch('/actions/form_add_chapters.php', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await res.json();
                    if (data.success) storedNames.push(...data.files);
                    else {
                        alert(`Erreur upload images pour le chapitre ${chapterName}`);
                        return;
                    }
                }
            }

            alert('Tous les chapitres ont été uploadés !');
            window.location.href = `/scan/${idScan}`;
        });
    </script> -->
</body>

</html>