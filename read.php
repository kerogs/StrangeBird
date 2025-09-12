<?php

require_once __DIR__ . '/includes/core.php';

// id and chap need to be a number
$id = intval($_GET['id']);
// ! chapter can be a int or a float
$chap = floatval($_GET['chap']);

// if id or chap is not a number, redirect to index
if (!is_numeric($id) || !is_numeric($chap)) {
    sendHttpError(403);
}

// ? scan information
$scanInfo = $pdo->prepare('SELECT * FROM "scan" WHERE id = :id');
$scanInfo->bindValue(':id', $id);
$scanInfo->execute();
$scanInfo = $scanInfo->fetch(PDO::FETCH_ASSOC);

// ? scan chapters contents
$scan = $pdo->prepare('SELECT * FROM "chapters" WHERE id_scan = :id AND number = :chap');
$scan->bindValue(':id', $id);
$scan->bindValue(':chap', $chap);
$scan->execute();
$scan = $scan->fetch(PDO::FETCH_ASSOC);

// if scan not found, show 404
if (!$scan) {
    sendHttpError(404);
}

$scanImgsUrls = "/uploads/chapters/$id/" . $scan['id'] . "/";
$imagesExp = explode('#~>', $scan['everyImagesLink']);

// ? all chapters
$allChapters = $pdo->prepare('SELECT * FROM "chapters" WHERE id_scan = :id ORDER BY number ASC');
$allChapters->bindValue(':id', $id);
$allChapters->execute();

$allChaptersByNumber = [];
$allChapterNumbers = []; // tableau pour les numéros triés

while ($chapter = $allChapters->fetch(PDO::FETCH_ASSOC)) {
    $num = (string)$chapter['number']; // clé string
    $allChaptersByNumber[$num] = $chapter;
    $allChapterNumbers[] = $num;
}

// trouver l'index du chapitre courant
$currentChap = (string)$chap;
$currentIndex = array_search($currentChap, $allChapterNumbers);

$previousChapter = $currentIndex > 0 ? $allChaptersByNumber[$allChapterNumbers[$currentIndex - 1]] : null;
$nextChapter = $currentIndex !== false && $currentIndex < count($allChapterNumbers) - 1 ? $allChaptersByNumber[$allChapterNumbers[$currentIndex + 1]] : null;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StrangeBird | Read</title>

    <link rel="stylesheet" href="/assets/styles/css/style.css">

    <?php require_once __DIR__ . '/includes/head.php' ?>

</head>

<body class="dark">

    <?php require_once __DIR__ . '/includes/header.php' ?>

    <div class="scanRead">
        <div class="title">
            <h2><a href="/scan/<?= $id ?>"><?= $scanInfo['name'] ?></a></h2>
            <br>
            <!-- Select with all chapters of the scan, when choose one, auto load the chapter -->
            <div class="selectChap">
                <?php if ($previousChapter) { ?>
                    <a href="/scan/<?= $id ?>/<?= $previousChapter['number'] ?>">
                        <button>
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                                <path d="M560-240 320-480l240-240 56 56-184 184 184 184-56 56Z" />
                            </svg>
                            previous
                        </button>
                    </a>
                <?php } ?>
                <select name="chapters" id="chapters">
                    <?php foreach ($allChaptersByNumber as $chapter) { ?>
                        <option value="/scan/<?= $id ?>/<?= $chapter['number'] ?>" <?php echo $chapter['number'] == $chap ? 'selected' : '' ?>>Chapter <?= $chapter['number'] ?></option>
                    <?php } ?>
                </select>
                <?php if ($nextChapter) { ?>
                    <a href="/scan/<?= $id ?>/<?= $nextChapter['number'] ?>">
                        <button class="right">
                            next
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                                <path d="M504-480 320-664l56-56 240 240-240 240-56-56 184-184Z" />
                            </svg>
                        </button>
                    </a>
                <?php } ?>
            </div>

            <script>
                document.getElementById('chapters').addEventListener('change', () => {
                    window.location.href = document.getElementById('chapters').value;
                });
            </script>
        </div>

        <div class="read">
            <?php

            foreach ($imagesExp as $img) {
                echo "<img src='$scanImgsUrls$img' alt=''>";
            }

            ?>
        </div>

        <div class="title" id="bottomSelectChap">
            <h2><a href="/scan/<?= $id ?>"><?= $scanInfo['name'] ?></a></h2>
            <br>
            <!-- Select with all chapters of the scan, when choose one, auto load the chapter -->
            <div class="selectChap">
                <?php if ($previousChapter) { ?>
                    <a href="/scan/<?= $id ?>/<?= $previousChapter['number'] ?>">
                        <button>
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                                <path d="M560-240 320-480l240-240 56 56-184 184 184 184-56 56Z" />
                            </svg>
                            previous
                        </button>
                    </a>
                <?php } ?>
                <select name="chapters" id="chapters2">
                    <?php foreach ($allChaptersByNumber as $chapter) { ?>
                        <option value="/scan/<?= $id ?>/<?= $chapter['number'] ?>" <?php echo $chapter['number'] == $chap ? 'selected' : '' ?>>Chapter <?= $chapter['number'] ?></option>
                    <?php } ?>
                </select>
                <?php if ($nextChapter) { ?>
                    <a href="/scan/<?= $id ?>/<?= $nextChapter['number'] ?>">
                        <button class="right">
                            next
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                                <path d="M504-480 320-664l56-56 240 240-240 240-56-56 184-184Z" />
                            </svg>
                        </button>
                    </a>
                <?php } ?>
            </div>

            <script>
                document.getElementById('chapters2').addEventListener('change', () => {
                    window.location.href = document.getElementById('chapters2').value;
                });
            </script>
        </div>
    </div>

    <div class="scrollTracker">
        <div class="progress"></div>
    </div>

    <script>
        // Configuration par défaut
        let config = {
            viewMode: 'manhwa',
            header: 'show',
            progressBar: 'show',
            manhwaSize: 'default',
            customSize: 50,
            mangaSizeHeight: false,
            mangaSizeWidth: false
        };

        // Charger la configuration depuis le localStorage si disponible
        function loadConfig() {
            const savedConfig = localStorage.getItem('readConfig');
            if (savedConfig) {
                config = {
                    ...config,
                    ...JSON.parse(savedConfig)
                };
                applyConfig();
            }
        }

        // Appliquer la configuration
        function applyConfig() {
            // Appliquer le mode de vue
            const viewModeButtons = document.querySelectorAll('#viewmode button');
            viewModeButtons.forEach(btn => btn.classList.remove('active'));
            document.querySelector(`#viewmode button[data-value="${config.viewMode}"]`).classList.add('active');

            // Appliquer le mode header
            const headerButtons = document.querySelectorAll('#header button');
            headerButtons.forEach(btn => btn.classList.remove('active'));
            document.querySelector(`#header button[data-value="${config.header}"]`).classList.add('active');
            applyHeaderStyle();

            // Appliquer la barre de progression
            const progressButtons = document.querySelectorAll('#progressbar button');
            progressButtons.forEach(btn => btn.classList.remove('active'));
            document.querySelector(`#progressbar button[data-value="${config.progressBar}"]`).classList.add('active');
            applyProgressBarStyle();

            // Appliquer la taille manhwa
            const sizeButtons = document.querySelectorAll('#size button');
            sizeButtons.forEach(btn => btn.classList.remove('active'));
            document.querySelector(`#size button[data-value="${config.manhwaSize}"]`).classList.add('active');
            applyManhwaSize();

            // Appliquer la taille personnalisée
            const sizeRange = document.querySelector('#sizeCustom input');
            const sizeValue = document.querySelector('#val');
            sizeRange.value = config.customSize;
            sizeValue.textContent = config.customSize;

            // Appliquer la taille manga
            const mangaHeightBtn = document.querySelector('#mangaSize button[data-value="height"]');
            const mangaWidthBtn = document.querySelector('#mangaSize button[data-value="width"]');

            if (mangaHeightBtn) mangaHeightBtn.classList.toggle('active', config.mangaSizeHeight);
            if (mangaWidthBtn) mangaWidthBtn.classList.toggle('active', config.mangaSizeWidth);

            // Afficher/masquer les sections en fonction du mode
            toggleSectionsVisibility();

            // Appliquer le mode de navigation
            applyViewMode();
        }

        // Afficher/masquer les sections en fonction du mode de vue
        function toggleSectionsVisibility() {
            const manhwaSection = document.querySelector('#manhwaSizeSection');
            const mangaSection = document.querySelector('#mangaSizeSection');

            if (config.viewMode === 'manhwa') {
                if (manhwaSection) manhwaSection.style.display = 'block';
                if (mangaSection) mangaSection.style.display = 'none';
            } else {
                if (manhwaSection) manhwaSection.style.display = 'none';
                if (mangaSection) mangaSection.style.display = 'block';
            }
        }

        // Appliquer le style du header
        function applyHeaderStyle() {
            const header = document.querySelector('header');

            if (config.header === 'hide') {
                header.style.display = 'none';
            } else {
                header.style.display = 'block';

                if (config.header === 'sticky') {
                    header.classList.add('sticky');
                } else {
                    header.classList.remove('sticky');
                }
            }
        }

        // Appliquer le style de la barre de progression
        function applyProgressBarStyle() {
            const progressBar = document.querySelector('.scrollTracker');

            if (config.progressBar === 'hide') {
                progressBar.style.display = 'none';
            } else {
                progressBar.style.display = 'block';
            }
        }

        // Appliquer la taille des images manhwa
        function applyManhwaSize() {
            const images = document.querySelectorAll('.read img');

            if (config.viewMode === 'manhwa') {
                if (config.manhwaSize === 'default') {
                    images.forEach(img => {
                        img.style.width = '';
                        img.style.maxWidth = '600px';
                        img.style.height = '';
                    });
                } else {
                    images.forEach(img => {
                        img.style.width = `${config.customSize}%`;
                        img.style.maxWidth = 'none';
                        img.style.height = '';
                    });
                }
            }
        }

        // Appliquer la taille des images manga
        function applyMangaSize() {
            const images = document.querySelectorAll('.read img');

            images.forEach(img => {
                img.style.width = config.mangaSizeWidth ? '100dvw' : '';
                img.style.height = config.mangaSizeHeight ? '100dvh' : '';
                img.style.objectFit = 'contain';
            });
        }

        // Appliquer le mode de vue (manhwa/manga)
        function applyViewMode() {
            const images = document.querySelectorAll('.read img');

            if (config.viewMode === 'manhwa') {
                // Mode manhwa - scroll vertical

                const bottomSelectChap = document.getElementById('bottomSelectChap');
                bottomSelectChap.style.display = 'block';

                // set .scanRead to width: var(--read-width);
                const scanRead = document.querySelector('.scanRead');
                scanRead.style.width = 'var(--read-width)';

                document.body.style.overflow = 'auto';
                images.forEach(img => {
                    img.style.cursor = 'default';
                    img.onclick = null;
                });

                // show every image again
                images.forEach(img => {
                    img.style.display = 'block';
                })
                applyManhwaSize();
            } else {
                // Mode manga - navigation par clic
                let currentImageIndex = 0;

                const bottomSelectChap = document.getElementById('bottomSelectChap');
                bottomSelectChap.style.display = 'none';

                // Appliquer la taille des images manga
                const scanRead = document.querySelector('.scanRead');
                // set width to unset
                scanRead.style.width = 'unset';

                applyMangaSize();

                // scroll to the image
                images[currentImageIndex].scrollIntoView({
                    block: 'center'
                });

                images.forEach((img, index) => {
                    if (index === 0) {
                        img.style.display = 'block';
                    } else {
                        img.style.display = 'none';
                    }

                    img.style.cursor = 'pointer';

                    img.onclick = (e) => {
                        const rect = img.getBoundingClientRect();
                        const clickX = e.clientX - rect.left;

                        if (clickX > rect.width / 2) {
                            // Clic à droite - image suivante
                            if (currentImageIndex < images.length - 1) {
                                images[currentImageIndex].style.display = 'none';
                                currentImageIndex++;
                                images[currentImageIndex].style.display = 'block';
                                updateMangaProgress();
                            } else if (currentImageIndex === images.length - 1 && <?= $nextChapter ? 'true' : 'false' ?>) {
                                // Dernière image, aller au chapitre suivant
                                window.location.href = '/scan/<?= $id ?>/<?= $nextChapter['number'] ?>';
                            }
                        } else {
                            // Clic à gauche - image précédente
                            if (currentImageIndex > 0) {
                                images[currentImageIndex].style.display = 'none';
                                currentImageIndex--;
                                images[currentImageIndex].style.display = 'block';
                                updateMangaProgress();
                            } else if (currentImageIndex === 0 && <?= $previousChapter ? 'true' : 'false' ?>) {
                                // Première image, aller au chapitre précédent
                                window.location.href = '/scan/<?= $id ?>/<?= $previousChapter['number'] ?>';
                            }
                        }
                    };
                });

                // Mettre à jour la barre de progression pour le mode manga
                updateMangaProgress();
            }
        }

        // Mettre à jour la barre de progression en mode manga
        function updateMangaProgress() {
            if (config.viewMode === 'manga') {
                const images = document.querySelectorAll('.read img');
                const visibleIndex = Array.from(images).findIndex(img => img.style.display !== 'none');
                const progressPercent = ((visibleIndex + 1) / images.length) * 100;
                document.querySelector('.progress').style.width = `${progressPercent}%`;
            }
        }

        // Sauvegarder la configuration
        function saveConfig() {
            localStorage.setItem('readConfig', JSON.stringify(config));
        }

        // Initialiser la configuration au chargement
        document.addEventListener('DOMContentLoaded', () => {
            loadConfig();

            // Configuration des boutons du menu
            document.querySelectorAll('#viewmode button').forEach(button => {
                button.addEventListener('click', () => {
                    document.querySelectorAll('#viewmode button').forEach(btn => btn.classList.remove('active'));
                    button.classList.add('active');
                    config.viewMode = button.getAttribute('data-value');
                    toggleSectionsVisibility();
                    applyViewMode();
                    saveConfig();
                });
            });

            document.querySelectorAll('#header button').forEach(button => {
                button.addEventListener('click', () => {
                    document.querySelectorAll('#header button').forEach(btn => btn.classList.remove('active'));
                    button.classList.add('active');
                    config.header = button.getAttribute('data-value');
                    applyHeaderStyle();
                    saveConfig();
                });
            });

            document.querySelectorAll('#progressbar button').forEach(button => {
                button.addEventListener('click', () => {
                    document.querySelectorAll('#progressbar button').forEach(btn => btn.classList.remove('active'));
                    button.classList.add('active');
                    config.progressBar = button.getAttribute('data-value');
                    applyProgressBarStyle();
                    saveConfig();
                });
            });

            document.querySelectorAll('#size button').forEach(button => {
                button.addEventListener('click', () => {
                    document.querySelectorAll('#size button').forEach(btn => btn.classList.remove('active'));
                    button.classList.add('active');
                    config.manhwaSize = button.getAttribute('data-value');
                    applyManhwaSize();
                    saveConfig();
                });
            });

            document.querySelector('#sizeCustom input').addEventListener('input', (e) => {
                config.customSize = e.target.value;
                document.querySelector('#val').textContent = e.target.value;
                if (config.manhwaSize === 'custom') {
                    applyManhwaSize();
                }
                saveConfig();
            });

            // Configuration des boutons de taille manga
            document.querySelectorAll('#mangaSize button').forEach(button => {
                button.addEventListener('click', () => {
                    const sizeType = button.getAttribute('data-value');

                    if (sizeType === 'height') {
                        config.mangaSizeHeight = !config.mangaSizeHeight;
                    } else if (sizeType === 'width') {
                        config.mangaSizeWidth = !config.mangaSizeWidth;
                    }

                    button.classList.toggle('active');
                    applyMangaSize();
                    saveConfig();
                });
            });

            // ? progress bar pour le mode manhwa
            window.addEventListener('scroll', () => {
                if (config.viewMode === 'manhwa') {
                    const scrollTop = window.scrollY;
                    const docHeight = document.documentElement.scrollHeight - window.innerHeight;
                    const scrollPercent = (scrollTop / docHeight) * 100;
                    document.querySelector('.progress').style.width = `${scrollPercent}%`;
                }
            });

            // ? keyboard navigation
            document.addEventListener('keydown', (event) => {
                if (config.viewMode === 'manhwa') {
                    // Navigation par chapitre en mode manhwa
                    if (event.key === 'ArrowLeft' && <?= $previousChapter ? 'true' : 'false' ?>) {
                        window.location.href = '/scan/<?= $id ?>/<?= $previousChapter['number'] ?>';
                    } else if (event.key === 'ArrowRight' && <?= $nextChapter ? 'true' : 'false' ?>) {
                        window.location.href = '/scan/<?= $id ?>/<?= $nextChapter['number'] ?>';
                    }
                } else {
                    // Navigation par image en mode manga
                    const images = document.querySelectorAll('.read img');
                    const visibleIndex = Array.from(images).findIndex(img => img.style.display !== 'none');

                    if (event.key === 'ArrowRight') {
                        // Image suivante ou chapitre suivant
                        if (visibleIndex < images.length - 1) {
                            images[visibleIndex].style.display = 'none';
                            images[visibleIndex + 1].style.display = 'block';
                            updateMangaProgress();
                        } else if (visibleIndex === images.length - 1 && <?= $nextChapter ? 'true' : 'false' ?>) {
                            window.location.href = '/scan/<?= $id ?>/<?= $nextChapter['number'] ?>';
                        }
                    } else if (event.key === 'ArrowLeft') {
                        // Image précédente ou chapitre précédent
                        if (visibleIndex > 0) {
                            images[visibleIndex].style.display = 'none';
                            images[visibleIndex - 1].style.display = 'block';
                            updateMangaProgress();
                        } else if (visibleIndex === 0 && <?= $previousChapter ? 'true' : 'false' ?>) {
                            window.location.href = '/scan/<?= $id ?>/<?= $previousChapter['number'] ?>';
                        }
                    }
                }
            });
        });
    </script>

    <div class="menu">
        <div class="title">
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                    <path d="m370-80-16-128q-13-5-24.5-12T307-235l-119 50L78-375l103-78q-1-7-1-13.5v-27q0-6.5 1-13.5L78-585l110-190 119 50q11-8 23-15t24-12l16-128h220l16 128q13 5 24.5 12t22.5 15l119-50 110 190-103 78q1 7 1 13.5v27q0 6.5-2 13.5l103 78-110 190-118-50q-11 8-23 15t-24 12L590-80H370Zm112-260q58 0 99-41t41-99q0-58-41-99t-99-41q-59 0-99.5 41T342-480q0 58 40.5 99t99.5 41Z" />
                </svg>
                Preferences
            </div>
            <div>
                <svg _="on click toggle .active on .menu" class="close" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                    <path d="M300-640v320l160-160-160-160ZM200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H200Zm360-80v-560H200v560h360Z" />
                </svg>
            </div>
        </div>

        <div class="container">
            <section>
                <h3>View mode</h3>
                <p class="description">
                    Choose your preferred view mode
                </p>

                <div id="viewmode" class="group">
                    <button data-value="manhwa" class="active">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                            <path d="M280-40q-33 0-56.5-23.5T200-120v-720q0-33 23.5-56.5T280-920h400q33 0 56.5 23.5T760-840v124q18 7 29 22t11 34v80q0 19-11 34t-29 22v404q0 33-23.5 56.5T680-40H280Zm200-280 160-160-56-56-64 62v-166h-80v166l-64-62-56 56 160 160Z" />
                        </svg>
                        Manhwa
                    </button>
                    <button data-value="manga">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                            <path d="M480-160q-48-38-104-59t-116-21q-42 0-82.5 11T100-198q-21 11-40.5-1T40-234v-482q0-11 5.5-21T62-752q46-24 96-36t102-12q58 0 113.5 15T480-740v484q51-32 107-48t113-16q36 0 70.5 6t69.5 18v-480q15 5 29.5 10.5T898-752q11 5 16.5 15t5.5 21v482q0 23-19.5 35t-40.5 1q-37-20-77.5-31T700-240q-60 0-116 21t-104 59Zm80-200v-380l200-200v400L560-360Z" />
                        </svg>
                        Manga
                    </button>
                </div>
            </section>

            <section>
                <h3>Header</h3>
                <p class="description">
                    Change the header style
                </p>

                <div id="header" class="group">
                    <button data-value="show" class="active">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                            <path d="M480-320q75 0 127.5-52.5T660-500q0-75-52.5-127.5T480-680q-75 0-127.5 52.5T300-500q0 75 52.5 127.5T480-320Zm0-72q-45 0-76.5-31.5T372-500q0-45 31.5-76.5T480-608q45 0 76.5 31.5T588-500q0 45-31.5 76.5T480-392Zm0 192q-146 0-266-81.5T40-500q54-137 174-218.5T480-800q146 0 266 81.5T920-500q-54 137-174 218.5T480-200Z" />
                        </svg>
                        Show
                    </button>
                    <button data-value="sticky">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                            <path d="M480-392q-45 0-76.5-31.5T372-500q0-45 31.5-76.5T480-608q45 0 76.5 31.5T588-500q0 45-31.5 76.5T480-392Zm0 192q-146 0-266-81.5T40-500q54-137 174-218.5T480-800q141 0 257.5 76T912-520H760q-28 0-53 7t-47 20v-7q0-75-52.5-127.5T480-680q-75 0-127.5 52.5T300-500q0 75 52.5 127.5T480-320q22 0 42.5-5t38.5-14q-1 5-1 9.5V-207q-20 3-40 5t-40 2Zm200 80q-17 0-28.5-11.5T640-160v-120q0-17 11.5-28.5T680-320v-40q0-33 23.5-56.5T760-440q33 0 56.5 23.5T840-360v40q17 0 28.5 11.5T880-280v120q0 17-11.5 28.5T840-120H680Zm40-200h80v-40q0-17-11.5-28.5T760-400q-17 0-28.5 11.5T720-360v40Z" />
                        </svg>
                        Sticky
                    </button>
                    <!-- 2 colomns -->
                    <button data-value="hide" style="grid-column: 1 / span 2">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                            <path d="M792-56 624-222q-35 11-70.5 16.5T480-200q-151 0-269-83.5T40-500q21-53 53-98.5t73-81.5L56-792l56-56 736 736-56 56ZM480-320q11 0 20.5-1t20.5-4L305-541q-3 11-4 20.5t-1 20.5q0 75 52.5 127.5T480-320Zm292 18L645-428q7-17 11-34.5t4-37.5q0-75-52.5-127.5T480-680q-20 0-37.5 4T408-664L306-766q41-17 84-25.5t90-8.5q151 0 269 83.5T920-500q-23 59-60.5 109.5T772-302ZM587-486 467-606q28-5 51.5 4.5T559-574q17 18 24.5 41.5T587-486Z" />
                        </svg>
                        Hide
                    </button>
                </div>
            </section>

            <section>
                <h3>Progress bar</h3>
                <p class="description">
                    Change the header style
                </p>

                <div id="progressbar" class="group">
                    <button data-value="show" class="active">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                            <path d="M480-320q75 0 127.5-52.5T660-500q0-75-52.5-127.5T480-680q-75 0-127.5 52.5T300-500q0 75 52.5 127.5T480-320Zm0-72q-45 0-76.5-31.5T372-500q0-45 31.5-76.5T480-608q45 0 76.5 31.5T588-500q0 45-31.5 76.5T480-392Zm0 192q-146 0-266-81.5T40-500q54-137 174-218.5T480-800q146 0 266 81.5T920-500q-54 137-174 218.5T480-200Z" />
                        </svg>
                        Show
                    </button>
                    <button data-value="hide">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                            <path d="M792-56 624-222q-35 11-70.5 16.5T480-200q-151 0-269-83.5T40-500q21-53 53-98.5t73-81.5L56-792l56-56 736 736-56 56ZM480-320q11 0 20.5-1t20.5-4L305-541q-3 11-4 20.5t-1 20.5q0 75 52.5 127.5T480-320Zm292 18L645-428q7-17 11-34.5t4-37.5q0-75-52.5-127.5T480-680q-20 0-37.5 4T408-664L306-766q41-17 84-25.5t90-8.5q151 0 269 83.5T920-500q-23 59-60.5 109.5T772-302ZM587-486 467-606q28-5 51.5 4.5T559-574q17 18 24.5 41.5T587-486Z" />
                        </svg>
                        Hide
                    </button>
                </div>
            </section>

            <section id="manhwaSizeSection">
                <h3>Manhwa width size</h3>
                <p class="description">
                    Change the width size when the view mode is manhwa. (Beta)
                </p>

                <div id="size" class="group">
                    <button data-value="default" class="active">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                            <path d="M200-80q-17 0-28.5-11.5T160-120q0-8 9.5-35.5T190-229q11-46 20.5-108.5T220-480q0-80-9.5-142.5T190-731q-11-46-20.5-73.5T160-840q0-17 11.5-28.5T200-880h560q17 0 28.5 11.5T800-840q0 8-9.5 35.5T770-731q-11 46-20.5 108.5T740-480q0 80 9.5 142.5T770-229q11 46 20.5 73.5T800-120q0 17-11.5 28.5T760-80H200Z" />
                        </svg>
                        Default size
                    </button>
                    <button data-value="custom">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                            <path d="M120-120v-720h80v720h-80Zm640 0v-720h80v720h-80ZM280-440v-80h80v80h-80Zm160 0v-80h80v80h-80Zm160 0v-80h80v80h-80Z" />
                        </svg>
                        Custom
                    </button>
                </div>

                <p class="description">
                    view size in percentage
                </p>
                <div id="sizeCustom">
                    <input type="range" min="10" max="100" value="50">
                    <label for="range"><span id="val">50</span>%</label>
                </div>
            </section>

            <section id="mangaSizeSection" style="display: none;">
                <h3>Manga image size</h3>
                <p class="description">
                    Adjust image size when in manga mode
                </p>

                <div id="mangaSize" class="group">
                    <button data-value="height">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                            <path d="M480-120 320-280l56-56 64 63v-414l-64 63-56-56 160-160 160 160-56 57-64-64v414l64-63 56 56-160 160Z" />
                        </svg>
                        Fit Height
                    </button>
                    <button data-value="width">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                            <path d="M120-120v-720h80v720h-80Zm640 0v-720h80v720h-80ZM280-440v-80h80v80h-80Zm160 0v-80h80v80h-80Zm160 0v-80h80v80h-80Z" />
                        </svg>
                        Fit Width
                    </button>
                </div>
                <p class="description">
                    You can enable both options simultaneously
                </p>
            </section>
        </div>
    </div>

    <div class="popupShow">
        <a href="/scan/<?= $id ?>/<?= $nextChapter['number'] ?>">
            <button>
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                    <path d="M504-480 320-664l56-56 240 240-240 240-56-56 184-184Z" />
                </svg>
            </button>
        </a>
        <!-- htmx : on click give .active to .menu -->
        <button _="on click toggle .active on .menu">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                <path d="m370-80-16-128q-13-5-24.5-12T307-235l-119 50L78-375l103-78q-1-7-1-13.5v-27q0-6.5 1-13.5L78-585l110-190 119 50q11-8 23-15t24-12l16-128h220l16 128q13 5 24.5 12t22.5 15l119-50 110 190-103 78q1 7 1 13.5v27q0 6.5-2 13.5l103 78-110 190-118-50q-11 8-23 15t-24 12L590-80H370Zm112-260q58 0 99-41t41-99q0-58-41-99t-99-41q-59 0-99.5 41T342-480q0 58 40.5 99t99.5 41Z" />
            </svg>
        </button>
    </div>

</body>

</html>