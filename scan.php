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

foreach ($chapters as &$ch) {
    $ch['id'] = (int) ($ch['id'] ?? 0);
    $ch['number'] = $ch['number'] ?? '0';
    $ch['name'] = $ch['name'] ?? '';

    $files = [];
    if (!empty($ch['everyImagesLink'])) {
        $parts = explode('#~>', $ch['everyImagesLink']);
        $parts = array_values(array_filter(array_map('trim', $parts), function ($v) {
            return $v !== '';
        }));
        $files = $parts;
    }
    $ch['images'] = $files;
    $ch['images_count'] = count($files);

    if (!empty($files)) {
        $first = $files[1];
        $ch['first_image_url'] = '/uploads/chapters/' . $scan['id'] . '/' . $ch['id'] . '/' . rawurlencode($first);
    } else {
        $ch['first_image_url'] = '/assets/img/default/cover_chapter.png';
    }

    $ch['uploaded_at'] = !empty($ch['datetime']) ? date('Y-m-d H:i', (int)$ch['datetime']) : '';
}

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

                <a href="/scan/<?= $scan['id'] ?>/1"><button>
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                            <path d="M480-160q-48-38-104-59t-116-21q-42 0-82.5 11T100-198q-21 11-40.5-1T40-234v-482q0-11 5.5-21T62-752q46-24 96-36t102-12q58 0 113.5 15T480-740v484q51-32 107-48t113-16q36 0 70.5 6t69.5 18v-480q15 5 29.5 10.5T898-752q11 5 16.5 15t5.5 21v482q0 23-19.5 35t-40.5 1q-37-20-77.5-31T700-240q-60 0-116 21t-104 59Zm80-200v-380l200-200v400L560-360Z" />
                        </svg>
                        Start reading
                    </button></a>

                <?php if ($scan['addedby_user_id']) { ?>
                    <a href="/add/chapters/<?= $scan['id'] ?>"><button class="secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                                <path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v268q-19-9-39-15.5t-41-9.5v-243H200v560h242q3 22 9.5 42t15.5 38H200Zm0-120v40-560 243-3 280Zm80-40h163q3-21 9.5-41t14.5-39H280v80Zm0-160h244q32-30 71.5-50t84.5-27v-3H280v80Zm0-160h400v-80H280v80ZM720-40q-83 0-141.5-58.5T520-240q0-83 58.5-141.5T720-440q83 0 141.5 58.5T920-240q0 83-58.5 141.5T720-40Zm-20-80h40v-100h100v-40H740v-100h-40v100H600v40h100v100Z" />
                            </svg>
                            Add chapters
                        </button></a>
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