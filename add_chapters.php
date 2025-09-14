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

// get all chapters from the scan
$chapters = $pdo->prepare('SELECT * FROM "chapters" WHERE id_scan = :id ORDER BY number ASC');
$chapters->bindValue(':id', $scanId);
$chapters->execute();
$chapters = $chapters->fetchAll(PDO::FETCH_ASSOC);

// get scan info
$scanInfo = $pdo->prepare('SELECT * FROM "scan" WHERE id = :id');
$scanInfo->bindValue(':id', $scanId);
$scanInfo->execute();
$scanInfo = $scanInfo->fetch(PDO::FETCH_ASSOC);

// check if user_id is the author of the scan
if ($scanInfo['addedby_user_id'] !== $_SESSION['user_id']) {
    sendHttpError(401);
    exit;
}
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

    <main>

        <div class="chapters-add">
            <h2>Add a chapter for <a href="/scan/<?= $scanId ?>"><?= $scanInfo['name'] ?></a></h2>

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

        <div class="chapters-add">
            <h2>or</h2>
            <form method="POST" action="/actions/form_add_chapters_zip.php" enctype="multipart/form-data">
                <input type="hidden" name="id_scan" value="<?= htmlspecialchars($scanId) ?>">

                <label>Upload a ZIP with all chapters and images *</label>
                <input type="file" name="chapters_zip" accept=".zip" required>

                <button type="submit" class="btn-submit">Upload all chapters</button>
            </form>
        </div>

        <!-- Section de suppression -->
        <div class="danger-zone">
            <h2>Danger Zone</h2>

            <!-- Formulaire pour supprimer un chapitre spécifique -->
            <?php if (count($chapters) > 0): ?>
                <div class="delete-section">
                    <h3>Delete a specific chapter</h3>
                    <form id="deleteChapterForm" method="POST" action="/actions/delete_chapter.php">
                        <input type="hidden" name="id_scan" value="<?= htmlspecialchars($scanId) ?>">

                        <label for="chapterSelect">Select chapter to delete:</label>
                        <select name="chapter_id" id="chapterSelect" required>
                            <?php foreach ($chapters as $chapter): ?>
                                <option value="<?= $chapter['id'] ?>">
                                    Chapter <?= $chapter['number'] ?> - <?= htmlspecialchars($chapter['name'] ?: 'No name') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <button type="button" class="btn-delete" onclick="confirmDeleteChapter()">
                            Delete Chapter
                        </button>
                    </form>
                </div>
            <?php endif; ?>

            <!-- Bouton pour supprimer tous les chapitres -->
            <div class="delete-section">
                <h3>Delete all chapters</h3>
                <form id="deleteAllChaptersForm" method="POST" action="/actions/delete_all_chapters.php">
                    <input type="hidden" name="id_scan" value="<?= htmlspecialchars($scanId) ?>">

                    <button type="button" class="btn-delete" onclick="confirmDeleteAllChapters()">
                        Delete All Chapters
                    </button>
                </form>
            </div>

            <!-- Bouton pour supprimer le scan complet -->
            <div class="delete-section">
                <h3>Delete entire scan</h3>
                <form id="deleteScanForm" method="POST" action="/actions/delete_scan.php">
                    <input type="hidden" name="id_scan" value="<?= htmlspecialchars($scanId) ?>">

                    <button type="button" class="btn-delete" onclick="confirmDeleteScan()">
                        Delete Entire Scan
                    </button>
                </form>
            </div>
        </div>

    </main>

    <script>
        // Drag & Drop functionality
        const dropArea = document.getElementById("dropArea");
        const fileInput = document.getElementById("fileInput");
        const preview = document.getElementById("preview");
        let filesToUpload = [];

        // Drag & Drop handlers
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
            const chunkSize = 10;
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

        // Functions de confirmation avec SweetAlert2
        function confirmDeleteChapter() {
            const chapterSelect = document.getElementById('chapterSelect');
            const selectedChapter = chapterSelect.options[chapterSelect.selectedIndex].text;

            Swal.fire({
                title: 'Are you sure?',
                html: `You are about to delete:<br><strong>${selectedChapter}</strong>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('deleteChapterForm').submit();
                }
            });
        }

        function confirmDeleteAllChapters() {
            Swal.fire({
                title: 'Are you sure?',
                html: 'You are about to delete <strong>ALL CHAPTERS</strong> of this scan.<br>This action cannot be undone!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete all!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('deleteAllChaptersForm').submit();
                }
            });
        }

        function confirmDeleteScan() {
            Swal.fire({
                title: 'Are you absolutely sure?',
                html: 'You are about to delete the <strong>ENTIRE SCAN</strong> including all chapters and images.<br><span style="color: #d33;">This action cannot be undone!</span>',
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete everything!',
                cancelButtonText: 'Cancel',
                focusCancel: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('deleteScanForm').submit();
                }
            });
        }
    </script>
</body>

</html>