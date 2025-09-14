<?php

require_once __DIR__ . '/includes/core.php';


$id = intval($_GET['id']);

// $scanRandom = $pdo->query("SELECT * FROM `scan` ORDER BY RANDOM() LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
$scan = $pdo->prepare("SELECT * FROM 'scan' WHERE id = :id");
$scan->execute(['id' => $id]);
$scan = $scan->fetch(PDO::FETCH_ASSOC);

// if scan not found, show 404
if (!$scan) {
    sendHttpError(404);
}


// ! scan

// if background is empty, use cover
if (empty($scan['background'])) {
    $scan['background'] = $scan['cover'];
}

// explode tags into array
$scan['tag'] = explode(',', $scan['tag']);







// ! get scan chapters
$stmt = $pdo->prepare("SELECT * FROM chapters WHERE id_scan = :id_scan ORDER BY number ASC, id ASC");
$stmt->execute(['id_scan' => $id]);
$chapters = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($chapters as $key => $ch) {
    $chapters[$key]['id'] = (int) ($ch['id'] ?? 0);
    $chapters[$key]['number'] = $ch['number'] ?? '0';
    $chapters[$key]['name'] = $ch['name'] ?? '';

    $files = [];
    if (!empty($ch['everyImagesLink'])) {
        $parts = explode('#~>', $ch['everyImagesLink']);
        $parts = array_values(array_filter(array_map('trim', $parts), function ($v) {
            return $v !== '';
        }));
        $files = $parts;
    }
    $chapters[$key]['images'] = $files;
    $chapters[$key]['images_count'] = count($files);

    if (!empty($files)) {
        // CORRECTION: Utiliser $files[0] au lieu de $files[1] pour la première image
        $first = $files[1];
        $chapters[$key]['first_image_url'] = '/uploads/chapters/' . $scan['id'] . '/' . $ch['id'] . '/' . rawurlencode($first);
    } else {
        $chapters[$key]['first_image_url'] = '/assets/img/default/cover_chapter.png';
    }

    $chapters[$key]['uploaded_at'] = !empty($ch['datetime']) ? date('Y-m-d H:i', (int)$ch['datetime']) : '';
}

// add +1 view
$pdo->prepare("UPDATE scan SET view = view + 1 WHERE id = :id")->execute(['id' => $id]);

// check if scan is saved
$stmt = $pdo->prepare("SELECT * FROM scan_save WHERE id_scan = :id_scan AND id_user = :id_user");
$stmt->execute([
    'id_scan' => $id,
    'id_user' => $_SESSION['user_id']
]);
$isSaved = $stmt->fetch() !== false;

// check if scan is liked or disliked or nothing (nothing=no like or dislike)
$stmt = $pdo->prepare("SELECT * FROM scan_like WHERE id_scan = :id_scan AND id_user = :id_user");
$stmt->execute([
    'id_scan' => $id,
    'id_user' => $_SESSION['user_id']
]);
$userLikeStatus = $stmt->fetch();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StrangeBird | Scan</title>

    <link rel="stylesheet" href="/assets/styles/css/style.css">

    <?php require_once __DIR__ . '/includes/head.php' ?>
</head>

<body class="dark">

    <?php require_once __DIR__ . '/includes/header.php' ?>

    <div class="scanHeader" style="background-image: url('<?= $scan['background'] ?>'); background-size: 100%;">
        <div class="scanFilter"></div>
        <div class="scanFilterLinear"></div>
    </div>

    <main>
        <div class="scanInfo">
            <div class="left">
                <div class="imgCover">
                    <img src="<?= $scan['cover'] ?>" alt="" class="cover-img"
                        onclick="openLightbox(this.src)">
                    <div class="filter"></div>
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                        <path d="M120-120v-240h80v104l124-124 56 56-124 124h104v80H120Zm480 0v-80h104L580-324l56-56 124 124v-104h80v240H600ZM324-580 200-704v104h-80v-240h240v80H256l124 124-56 56Zm312 0-56-56 124-124H600v-80h240v240h-80v-104L636-580ZM480-400q-33 0-56.5-23.5T400-480q0-33 23.5-56.5T480-560q33 0 56.5 23.5T560-480q0 33-23.5 56.5T480-400Z" />
                    </svg>
                </div>

                <?php if ($auth->isLoggedIn()) { ?>
                    <div class="actionsBtn">
                        <!-- save scan -->
                        <button class="save-btn <?= $isSaved ? 'saved' : '' ?>" onclick="toggleSave(<?= $scan['id'] ?>)">
                            <!-- if no save -->
                            <svg class="unsaved-icon" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" <?= $isSaved ? 'style="display: none;"' : '' ?>>
                                <path d="M200-120v-640q0-33 23.5-56.5T280-840h400q33 0 56.5 23.5T760-760v640L480-240 200-120Zm80-122 200-86 200 86v-518H280v518Zm0-518h400-400Z" />
                            </svg>
                            <!-- if saved -->
                            <svg class="saved-icon" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" <?= $isSaved ? '' : 'style="display: none;"' ?>>
                                <path d="M200-120v-640q0-33 23.5-56.5T280-840h400q33 0 56.5 23.5T760-760v640L480-240 200-120Z" />
                            </svg>
                        </button>

                        <!-- Bouton Like -->
                        <button class="like-btn <?= $userLikeStatus['opinion'] === 'like' ? 'liked' : '' ?>" onclick="toggleLike(<?= $scan['id'] ?>, 'like')">
                            <!-- if no like -->
                            <svg class="like-icon-default" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" <?= $userLikeStatus['opinion'] === 'like' ? 'style="display: none;"' : '' ?>>
                                <path d="M720-120H280v-520l280-280 50 50q7 7 11.5 19t4.5 23v14l-44 174h258q32 0 56 24t24 56v80q0 7-2 15t-4 15L794-168q-9 20-30 34t-44 14Zm-360-80h360l120-280v-80H480l54-220-174 174v406Zm0-406v406-406Zm-80-34v80H160v360h120v80H80v-520h200Z" />
                            </svg>
                            <!-- if liked -->
                            <svg class="like-icon-active" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" <?= $userLikeStatus['opinion'] === 'like' ? '' : 'style="display: none;"' ?>>
                                <path d="M720-120H320v-520l280-280 50 50q7 7 11.5 19t4.5 23v14l-44 174h218q32 0 56 24t24 56v80q0 7-1.5 15t-4.5 15L794-168q-9 20-30 34t-44 14ZM240-640v520H80v-520h160Z" />
                            </svg>
                        </button>

                        <!-- Bouton Dislike -->
                        <button class="dislike-btn <?= $userLikeStatus['opinion'] === 'dislike' ? 'disliked' : '' ?>" onclick="toggleLike(<?= $scan['id'] ?>, 'dislike')">
                            <!-- if no dislike -->
                            <svg class="dislike-icon-default" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3" <?= $userLikeStatus['opinion'] === 'dislike' ? 'style="display: none;"' : '' ?>>
                                <path d="M240-840h440v520L400-40l-50-50q-7-7-11.5-19t-4.5-23v-14l44-174H120q-32 0-56-24t-24-56v-80q0-7 2-15t4-15l120-282q9-20 30-34t44-14Zm360 80H240L120-480v80h360l-54 220 174-174v-406Zm0 406v-406 406Zm80 34v-80h120v-360H680v-80h200v520H680Z" />
                            </svg>
                            <!-- if disliked -->
                            <svg class="dislike-icon-active" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="var(--orange-contrast)" <?= $userLikeStatus['opinion'] === 'dislike' ? '' : 'style="display: none;"' ?>>
                                <path d="M240-840h400v520L360-40l-50-50q-7-7-11.5-19t-4.5-23v-14l44-174H120q-32 0-56-24t-24-56v-80q0-7 1.5-15t4.5-15l120-282q9-20 30-34t44-14Zm480 520v-520h160v520H720Z" />
                            </svg>
                        </button>

                        <script>
                            // Fonctions globales
                            async function toggleSave(scanId) {
                                try {
                                    const response = await fetch('/api/scan_save.php', {
                                        method: 'POST',
                                        credentials: 'include',
                                        headers: {
                                            'Content-Type': 'application/json',
                                        },
                                        body: JSON.stringify({
                                            scan_id: scanId
                                        })
                                    });

                                    const data = await response.json();

                                    if (data.success) {
                                        // Mettre à jour l'UI
                                        const btn = document.querySelector(`.save-btn[onclick="toggleSave(${scanId})"]`);
                                        if (btn) {
                                            btn.classList.toggle('saved', data.saved);

                                            // Changer les icônes
                                            const unsavedIcon = btn.querySelector('.unsaved-icon');
                                            const savedIcon = btn.querySelector('.saved-icon');

                                            if (unsavedIcon && savedIcon) {
                                                unsavedIcon.style.display = data.saved ? 'none' : 'block';
                                                savedIcon.style.display = data.saved ? 'block' : 'none';
                                            }

                                            ntf(data.saved ? 'save_a' : 'save_r');
                                        }
                                    } else {
                                        throw new Error(data.error);
                                    }
                                } catch (error) {
                                    console.error('Erreur:', error);
                                    Swal.fire('Erreur', error.message || 'Une erreur est survenue', 'error');
                                }
                            }

                            async function toggleLike(scanId, type) {
                                try {
                                    const response = await fetch('/api/scan_like.php', {
                                        method: 'POST',
                                        credentials: 'include',
                                        headers: {
                                            'Content-Type': 'application/json',
                                        },
                                        body: JSON.stringify({
                                            scan_id: scanId,
                                            type: type
                                        })
                                    });

                                    const data = await response.json();

                                    if (data.success) {
                                        // Mettre à jour l'UI
                                        updateLikeUI(scanId, data.action, data.type);

                                        // Notification
                                        let message = '';
                                        if (data.action === 'added') {
                                            message = data.type === 'like' ? 'like_a' : 'dislike_a';
                                        } else {
                                            message = 'opinion_r';
                                        }

                                        ntf(message);
                                    } else {
                                        throw new Error(data.error);
                                    }
                                } catch (error) {
                                    console.error('Erreur:', error);
                                    Swal.fire('Erreur', error.message || 'Une erreur est survenue', 'error');
                                }
                            }

                            function updateLikeUI(scanId, action, type) {
                                const likeBtn = document.querySelector(`.like-btn[onclick="toggleLike(${scanId}, 'like')"]`);
                                const dislikeBtn = document.querySelector(`.dislike-btn[onclick="toggleLike(${scanId}, 'dislike')"]`);

                                // Icônes like
                                const likeDefault = likeBtn.querySelector('.like-icon-default');
                                const likeActive = likeBtn.querySelector('.like-icon-active');

                                // Icônes dislike
                                const dislikeDefault = dislikeBtn.querySelector('.dislike-icon-default');
                                const dislikeActive = dislikeBtn.querySelector('.dislike-icon-active');

                                if (action === 'added') {
                                    if (type === 'like') {
                                        // Activer like, désactiver dislike
                                        likeBtn.classList.add('liked');
                                        dislikeBtn.classList.remove('disliked');

                                        // Changer les icônes
                                        if (likeDefault && likeActive) {
                                            likeDefault.style.display = 'none';
                                            likeActive.style.display = 'block';
                                        }
                                        if (dislikeDefault && dislikeActive) {
                                            dislikeDefault.style.display = 'block';
                                            dislikeActive.style.display = 'none';
                                        }
                                    } else {
                                        // Activer dislike, désactiver like
                                        dislikeBtn.classList.add('disliked');
                                        likeBtn.classList.remove('liked');

                                        // Changer les icônes
                                        if (likeDefault && likeActive) {
                                            likeDefault.style.display = 'block';
                                            likeActive.style.display = 'none';
                                        }
                                        if (dislikeDefault && dislikeActive) {
                                            dislikeDefault.style.display = 'none';
                                            dislikeActive.style.display = 'block';
                                        }
                                    }
                                } else {
                                    // Retirer toutes les réactions
                                    likeBtn.classList.remove('liked');
                                    dislikeBtn.classList.remove('disliked');

                                    // Réinitialiser les icônes
                                    if (likeDefault && likeActive) {
                                        likeDefault.style.display = 'block';
                                        likeActive.style.display = 'none';
                                    }
                                    if (dislikeDefault && dislikeActive) {
                                        dislikeDefault.style.display = 'block';
                                        dislikeActive.style.display = 'none';
                                    }
                                }
                            }
                        </script>
                    </div>
                <?php } ?>

                <!-- if no chapter hide -->

                <?php if ($chapters) { ?>

                    <a href="/scan/<?= $scan['id'] ?>/1">
                        <button>
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                                <path d="M480-160q-48-38-104-59t-116-21q-42 0-82.5 11T100-198q-21 11-40.5-1T40-234v-482q0-11 5.5-21T62-752q46-24 96-36t102-12q58 0 113.5 15T480-740v484q51-32 107-48t113-16q36 0 70.5 6t69.5 18v-480q15 5 29.5 10.5T898-752q11 5 16.5 15t5.5 21v482q0 23-19.5 35t-40.5 1q-37-20-77.5-31T700-240q-60 0-116 21t-104 59Zm80-200v-380l200-200v400L560-360Z" />
                            </svg>
                            Start reading
                        </button>
                    </a>

                <?php } ?>

                <?php if ($scan['addedby_user_id'] === $_SESSION['user_id']) { ?>
                    <a href="/add/chapters/<?= $scan['id'] ?>">
                        <button class="secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                                <path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v268q-19-9-39-15.5t-41-9.5v-243H200v560h242q3 22 9.5 42t15.5 38H200Zm0-120v40-560 243-3 280Zm80-40h163q3-21 9.5-41t14.5-39H280v80Zm0-160h244q32-30 71.5-50t84.5-27v-3H280v80Zm0-160h400v-80H280v80ZM720-40q-83 0-141.5-58.5T520-240q0-83 58.5-141.5T720-440q83 0 141.5 58.5T920-240q0 83-58.5 141.5T720-40Zm-20-80h40v-100h100v-40H740v-100h-40v100H600v40h100v100Z" />
                            </svg>
                            Add chapters
                        </button>
                    </a>
                <?php } ?>
            </div>
            <div class="right">
                <h2><?= $scan['name'] ?></h2>
                <p class="tags">
                    <?php foreach ($scan['tag'] as $tag): ?>
                        <?php
                        $cleanTag = trim(strtolower($tag));
                        $class = $specialTags[$cleanTag] ?? '';
                        ?>
                        <span class="<?= $class ?>"><?= htmlspecialchars($tag) ?></span>
                    <?php endforeach; ?>
                </p>
                <p class="description">
                    <?= $scan['description'] ?>
                </p>
            </div>
        </div>

        <div class="chapters-controls">
            <input type="text" id="chapter-search" placeholder="Search for a chapter...">
            <button type="button" id="chapter-toggle-order">Change order</button>
        </div>

        <div class="chapters-list">
            <?php if (empty($chapters)): ?>
                <p>No chapters here...</p>
            <?php else: ?>
                <?php foreach ($chapters as $ch): ?>
                    <a class="chapter-card" href="/scan/<?= $scan['id'] ?>/<?= $ch['number'] ?>">
                        <div class="chapter-thumb" style="background-image: url('<?= htmlspecialchars($ch['first_image_url']) ?>');"></div>
                        <div class="chapter-meta">
                            <h3>Chapter <?= $ch['number'] ?><?= $ch['name'] ? ' — ' . htmlspecialchars($ch['name']) : '' ?></h3>
                            <p><?= $ch['images_count'] ?> page<?= $ch['images_count'] > 1 ? 's' : '' ?><?= $ch['uploaded_at'] ? ' • ' . $ch['uploaded_at'] : '' ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('chapter-search');
            const toggleBtn = document.getElementById('chapter-toggle-order');
            const listContainer = document.querySelector('.chapters-list');
            let asc = true;

            searchInput.addEventListener('input', () => {
                const query = searchInput.value.trim().toLowerCase();
                const isNumberQuery = /^[0-9]*\.?[0-9]*$/.test(query);
                listContainer.querySelectorAll('.chapter-card').forEach(card => {
                    const title = card.querySelector('.chapter-meta h3').textContent.toLowerCase();
                    const numMatch = title.match(/Chapitre\s+([0-9]+(?:\.[0-9]+)?)/i);
                    let number = numMatch ? numMatch[1] : '';

                    let show;
                    if (isNumberQuery && number) {
                        show = number.startsWith(query);
                    } else {
                        show = title.includes(query);
                    }
                    card.style.display = show ? '' : 'none';
                });
            });

            toggleBtn.addEventListener('click', () => {
                asc = !asc;
                const cards = Array.from(listContainer.querySelectorAll('.chapter-card')).filter(c => c.style.display !== 'none');
                cards.sort((a, b) => {
                    const numA = parseFloat(a.querySelector('.chapter-meta h3').textContent.match(/Chapitre\s+([0-9]+(?:\.[0-9]+)?)/i)?.[1] || 0);
                    const numB = parseFloat(b.querySelector('.chapter-meta h3').textContent.match(/Chapitre\s+([0-9]+(?:\.[0-9]+)?)/i)?.[1] || 0);
                    return asc ? numA - numB : numB - numA;
                });
                cards.forEach(c => listContainer.appendChild(c));
            });
        });
    </script>

    <!-- lightbox -->
    <div class="image-lightbox" id="lightbox" onclick="if(event.target==this)this.style.display='none'">
        <img id="lightbox-img" src="">
    </div>

    <script>
        function openLightbox(src) {
            const lb = document.getElementById('lightbox');
            const img = document.getElementById('lightbox-img');
            img.src = src;
            lb.style.display = 'flex';
        }
    </script>


















    <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
    <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>

</body>

</html>