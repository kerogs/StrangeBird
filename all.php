<?php

require_once __DIR__ . '/includes/core.php';

$searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';
$sortOrder = isset($_GET['sort-order']) ? $_GET['sort-order'] : 'recent';
$a_z = isset($_GET['a-z']) ? true : false;
$mostview = isset($_GET['mostview']) ? true : false;
$nosaved = isset($_GET['nosaved']) ? true : false;
$saved = isset($_GET['saved']) ? true : false;
$nolikenodislike = isset($_GET['nolikenodislike']) ? true : false;
$liked = isset($_GET['liked']) ? true : false;
$disliked = isset($_GET['disliked']) ? true : false;

$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

$query = 'SELECT s.* FROM scan s';
$countQuery = 'SELECT COUNT(*) as total FROM scan s';
$whereConditions = [];
$params = [];
$joinClauses = [];

if (!empty($searchTerm)) {
    $whereConditions[] = 's.name LIKE :search';
    $params[':search'] = '%' . $searchTerm . '%';
}

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    if ($nosaved) {
        $joinClauses[] = "LEFT JOIN scan_save ss_nosave ON s.id = ss_nosave.id_scan AND ss_nosave.id_user = :user_id_nosave";
        $whereConditions[] = 'ss_nosave.id_scan IS NULL';
        $params[':user_id_nosave'] = $userId;
    }

    if ($saved) {
        $joinClauses[] = "JOIN scan_save ss_save ON s.id = ss_save.id_scan AND ss_save.id_user = :user_id_save";
        $params[':user_id_save'] = $userId;
    }

    if ($nolikenodislike) {
        $joinClauses[] = "LEFT JOIN scan_like sl_none ON s.id = sl_none.id_scan AND sl_none.id_user = :user_id_none";
        $whereConditions[] = 'sl_none.id_scan IS NULL';
        $params[':user_id_none'] = $userId;
    }

    if ($liked) {
        $joinClauses[] = "JOIN scan_like sl_like ON s.id = sl_like.id_scan AND sl_like.id_user = :user_id_like AND sl_like.opinion = 'like'";
        $params[':user_id_like'] = $userId;
    }

    if ($disliked) {
        $joinClauses[] = "JOIN scan_like sl_dislike ON s.id = sl_dislike.id_scan AND sl_dislike.id_user = :user_id_dislike AND sl_dislike.opinion = 'dislike'";
        $params[':user_id_dislike'] = $userId;
    }
}

$whereClause = '';
if (!empty($whereConditions)) {
    $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
}

$joinClause = implode(' ', $joinClauses);

$orderBy = 'ORDER BY ';
if ($a_z) {
    $orderBy .= 's.name ASC';
} elseif ($mostview) {
    $orderBy .= 's.view DESC';
} else {
    $orderBy .= ($sortOrder === 'recent') ? 's.id DESC' : 's.id ASC';
}

$finalQuery = "$query $joinClause $whereClause $orderBy LIMIT :limit OFFSET :offset";
$finalCountQuery = "$countQuery $joinClause $whereClause";

$stmt = $pdo->prepare($finalQuery);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$scans = $stmt->fetchAll();

$countStmt = $pdo->prepare($finalCountQuery);
foreach ($params as $key => $value) {
    $countStmt->bindValue($key, $value);
}
$countStmt->execute();
$totalResults = $countStmt->fetch()['total'];
$totalPages = ceil($totalResults / $perPage);

$userScansData = [];
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    $saveStmt = $pdo->prepare("SELECT id_scan FROM scan_save WHERE id_user = :user_id");
    $saveStmt->execute([':user_id' => $userId]);
    $savedScans = $saveStmt->fetchAll(PDO::FETCH_COLUMN);

    $likeStmt = $pdo->prepare("SELECT id_scan, opinion FROM scan_like WHERE id_user = :user_id");
    $likeStmt->execute([':user_id' => $userId]);
    $userLikes = $likeStmt->fetchAll(PDO::FETCH_KEY_PAIR);

    foreach ($scans as $scan) {
        $scanId = $scan['id'];
        $userScansData[$scanId] = [
            'saved' => in_array($scanId, $savedScans),
            'like_status' => $userLikes[$scanId] ?? null
        ];
    }
}

// if there an GET option with for value "on" so $filterActivate = true
$filterActivate = false;

foreach ($_GET as $value) {
    if ($value == "on") {
        $filterActivate = true;
        break;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StrangeBird | All</title>

    <link rel="stylesheet" href="/assets/styles/css/style.css">

    <?php require_once __DIR__ . '/includes/head.php' ?>
</head>

<body class="dark">

    <?php require_once __DIR__ . '/includes/header.php' ?>

    <main>
        <div class="allSearch">
            <form class="allSearchForm" action="">
                <div class="dualBtn">
                    <div class="group">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                            <path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z" />
                        </svg>
                        <input type="search" name="q" placeholder="Search..." value="<?= $searchTerm ?>">
                    </div>
                    <button type="submit">Search</button>
                    <!-- htmx : on click toggle .active to .filter and me -->
                    <div class="filter-btn <?= $filterActivate ? 'active' : '' ?>" _="on click toggle .active on .filterToggleActiveFromFilter-btn then toggle .active on me">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                            <path d="M440-160q-17 0-28.5-11.5T400-200v-240L168-736q-15-20-4.5-42t36.5-22h560q26 0 36.5 22t-4.5 42L560-440v240q0 17-11.5 28.5T520-160h-80Z" />
                        </svg>
                    </div>
                </div>
                <div class="filter filterToggleActiveFromFilter-btn <?= $filterActivate ? 'active' : '' ?>">

                    <!-- sort by -->
                    <h2 style="margin-top:0;">Sort by</h2>
                    <div class="filterArea">
                        <div class="group">
                            <input type="checkbox" name="a-z" id="a-z" <?= isset($_GET['a-z']) ? 'checked' : '' ?>>
                            <label for="a-z">
                                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                                    <path d="m80-280 150-400h86l150 400h-82l-34-96H196l-32 96H80Zm140-164h104l-48-150h-6l-50 150Zm328 164v-76l202-252H556v-72h282v76L638-352h202v72H548ZM360-760l120-120 120 120H360ZM480-80 360-200h240L480-80Z" />
                                </svg>
                            </label>
                        </div>
                        <div class="group">
                            <input type="checkbox" name="mostview" id="mostview" <?= isset($_GET['mostview']) ? 'checked' : '' ?>>
                            <label for="mostview">
                                Most view
                            </label>
                        </div>
                    </div>


                    <div class="dualfilter">
                        <section>
                            <h2>Filter by</h2>
                            <div class="filterArea">
                                <div class="group">
                                    <input type="radio" name="sort-order" id="sort-recent" value="recent" <?= $_GET['sort-order'] == 'recent' || !isset($_GET['sort-order']) ? 'checked' : '' ?> value=<?= $_GET['sort'] ?>>
                                    <label for="sort-recent">
                                        Lastest
                                    </label>
                                </div>

                                <div class="group">
                                    <input type="radio" name="sort-order" id="sort-oldest" value="oldest" <?= $_GET['sort-order'] == 'oldest' ? 'checked' : '' ?>>
                                    <label for="sort-oldest">
                                        Oldest
                                    </label>
                                </div>
                            </div>
                        </section>
                        <?php if ($auth->isLoggedIn()) { ?>
                            <section>
                                <h2>My activity</h2>
                                <div class="filterArea">

                                    <div class="group">
                                        <input type="checkbox" name="nosaved" id="nosaved" <?= isset($_GET['nosaved']) ? 'checked' : '' ?>>
                                        <label for="nosaved">
                                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                                                <path d="M200-120v-640q0-33 23.5-56.5T280-840h240v80H280v518l200-86 200 86v-278h80v400L480-240 200-120Zm80-640h240-240Zm400 160v-80h-80v-80h80v-80h80v80h80v80h-80v80h-80Z" />
                                            </svg>
                                        </label>
                                    </div>

                                    <div class="group">
                                        <input type="checkbox" name="saved" id="saved" <?= isset($_GET['saved']) ? 'checked' : '' ?>>
                                        <label for="saved">
                                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                                                <path d="M200-120v-640q0-33 23.5-56.5T280-840h400q33 0 56.5 23.5T760-760v640L480-240 200-120Z" />
                                            </svg>
                                        </label>
                                    </div>

                                    <div class="group">
                                        <input type="checkbox" name="nolikenodislike" id="nolikenodislike" <?= isset($_GET['nolikenodislike']) ? 'checked' : '' ?>>
                                        <label for="nolikenodislike">
                                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                                                <path d="M80-400q-33 0-56.5-23.5T0-480v-240q0-12 5-23t13-19l198-198 30 30q6 6 10 15.5t4 18.5v8l-28 128h208q17 0 28.5 11.5T480-720v50q0 6-1 11.5t-3 10.5l-90 212q-7 17-22.5 26.5T330-400H80Zm238-80 82-194v-6H134l24-108-78 76v232h238ZM744 0l-30-30q-6-6-10-15.5T700-64v-8l28-128H520q-17 0-28.5-11.5T480-240v-50q0-6 1-11.5t3-10.5l90-212q8-17 23-26.5t33-9.5h250q33 0 56.5 23.5T960-480v240q0 12-4.5 22.5T942-198L744 0ZM642-480l-82 194v6h266l-24 108 78-76v-232H642Zm-562 0v-232 232Zm800 0v232-232Z" />
                                            </svg>
                                        </label>
                                    </div>

                                    <div class="group">
                                        <input type="checkbox" name="liked" id="liked" <?= isset($_GET['liked']) ? 'checked' : '' ?>>
                                        <label for="liked">
                                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                                                <path d="M720-120H320v-520l280-280 50 50q7 7 11.5 19t4.5 23v14l-44 174h218q32 0 56 24t24 56v80q0 7-1.5 15t-4.5 15L794-168q-9 20-30 34t-44 14ZM240-640v520H80v-520h160Z" />
                                            </svg>
                                        </label>
                                    </div>

                                    <div class="group">
                                        <input type="checkbox" name="disliked" id="disliked" <?= isset($_GET['disliked']) ? 'checked' : '' ?>>
                                        <label for="disliked">
                                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                                                <path d="M240-840h400v520L360-40l-50-50q-7-7-11.5-19t-4.5-23v-14l44-174H120q-32 0-56-24t-24-56v-80q0-7 1.5-15t4.5-15l120-282q9-20 30-34t44-14Zm480 520v-520h160v520H720Z" />
                                            </svg>
                                        </label>
                                    </div>

                                </div>
                            </section>
                        <?php } ?>
                    </div>
                </div>
            </form>


            <?php if (count($scans) > 0): ?>
                <div class="all">
                    <?php foreach ($scans as $scan): ?>
                        <?php

                        // check if user is logged
                        if ($auth->isLoggedIn()) {
                            // search if scan is liked
                            $stmt = $pdo->prepare("SELECT * FROM scan_like WHERE id_scan = :id_scan AND id_user = :id_user");
                            $stmt->execute([
                                'id_scan' => $scan['id'],
                                'id_user' => $_SESSION['user_id']
                            ]);
                            $isLiked = $stmt->fetch();

                            // search if scan is saved
                            $stmt = $pdo->prepare("SELECT * FROM scan_save WHERE id_scan = :id_scan AND id_user = :id_user");
                            $stmt->execute([
                                'id_scan' => $scan['id'],
                                'id_user' => $_SESSION['user_id']
                            ]);
                            $isSaved = $stmt->fetch();
                        }


                        // if timestamp is less than 7 days, add "new" tag
                        if (time() - (int)$scan['datetime'] < 60 * 60 * 24 * 7) {
                            $itsNew = true;
                        }


                        ?>
                        <div class="all__item <?= $itsNew ? 'newscan' : '' ?>">
                            <a href="/scan/<?= $scan['id'] ?>"></a>
                            <div class="cover">
                                <img src="<?= $scan['cover'] ?>" alt="<?= htmlspecialchars($scan['name']) ?>" onerror="this.src='/assets/img/templates/cover.webp'">

                                <?php if ($itsNew) { ?>
                                    <span class="newscan">New Scan</span>
                                <?php } ?>

                                <div class="filter"></div>
                                <svg class="link-icon" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                                    <path d="M240-40H120q-33 0-56.5-23.5T40-120v-120h80v120h120v80Zm480 0v-80h120v-120h80v120q0 33-23.5 56.5T840-40H720ZM480-220q-120 0-217.5-71T120-480q45-118 142.5-189T480-740q120 0 217.5 71T840-480q-45 118-142.5 189T480-220Zm0-120q58 0 99-41t41-99q0-58-41-99t-99-41q-58 0-99 41t-41 99q0 58 41 99t99 41Zm0-80q-25 0-42.5-17.5T420-480q0-25 17.5-42.5T480-540q25 0 42.5 17.5T540-480q0 25-17.5 42.5T480-420ZM40-720v-120q0-33 23.5-56.5T120-920h120v80H120v120H40Zm800 0v-120H720v-80h120q33 0 56.5 23.5T920-840v120h-80Z" />
                                </svg>

                                <div class="stats-bottom">
                                    <span class="views">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                                            <path d="M480-320q75 0 127.5-52.5T660-500q0-75-52.5-127.5T480-680q-75 0-127.5 52.5T300-500q0 75 52.5 127.5T480-320Zm0-72q-45 0-76.5-31.5T372-500q0-45 31.5-76.5T480-608q45 0 76.5 31.5T588-500q0 45-31.5 76.5T480-392Zm0 192q-146 0-266-81.5T40-500q54-137 174-218.5T480-800q146 0 266 81.5T920-500q-54 137-174 218.5T480-200Z" />
                                        </svg>
                                        <?= $scan['view'] ?>
                                    </span>

                                    <?php if ($auth->isLoggedIn()) { ?>
                                        <?php if ($isSaved) { ?>
                                            <span class="save">
                                                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                                                    <path d="M200-120v-640q0-33 23.5-56.5T280-840h400q33 0 56.5 23.5T760-760v640L480-240 200-120Z" />
                                                </svg>
                                            </span>
                                        <?php } else { ?>
                                            <span class="save">
                                                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                                                    <path d="M200-120v-640q0-33 23.5-56.5T280-840h240v80H280v518l200-86 200 86v-278h80v400L480-240 200-120Zm80-640h240-240Zm400 160v-80h-80v-80h80v-80h80v80h80v80h-80v80h-80Z" />
                                                </svg>
                                            </span>
                                        <?php } ?>

                                        <?php if (!$isLiked) { ?>
                                            <span class="like">
                                                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                                                    <path d="M80-400q-33 0-56.5-23.5T0-480v-240q0-12 5-23t13-19l198-198 30 30q6 6 10 15.5t4 18.5v8l-28 128h208q17 0 28.5 11.5T480-720v50q0 6-1 11.5t-3 10.5l-90 212q-7 17-22.5 26.5T330-400H80Zm238-80 82-194v-6H134l24-108-78 76v232h238ZM744 0l-30-30q-6-6-10-15.5T700-64v-8l28-128H520q-17 0-28.5-11.5T480-240v-50q0-6 1-11.5t3-10.5l90-212q8-17 23-26.5t33-9.5h250q33 0 56.5 23.5T960-480v240q0 12-4.5 22.5T942-198L744 0ZM642-480l-82 194v6h266l-24 108 78-76v-232H642Zm-562 0v-232 232Zm800 0v232-232Z" />
                                                </svg>
                                            </span>
                                        <?php } elseif ($isLiked['opinion'] === "like") { ?>
                                            <span class="like">
                                                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                                                    <path d="M720-120H320v-520l280-280 50 50q7 7 11.5 19t4.5 23v14l-44 174h218q32 0 56 24t24 56v80q0 7-1.5 15t-4.5 15L794-168q-9 20-30 34t-44 14ZM240-640v520H80v-520h160Z" />
                                                </svg>
                                            </span>
                                        <?php } else { ?>
                                            <span class="like">
                                                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                                                    <path d="M240-840h400v520L360-40l-50-50q-7-7-11.5-19t-4.5-23v-14l44-174H120q-32 0-56-24t-24-56v-80q0-7 1.5-15t4.5-15l120-282q9-20 30-34t44-14Zm480 520v-520h160v520H720Z" />
                                                </svg>
                                            </span>
                                        <?php } ?>
                                    <?php } ?>

                                    <span class="stars">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                                            <path d="m233-120 65-281L80-590l288-25 112-265 112 265 288 25-218 189 65 281-247-149-247 149Z" />
                                        </svg>
                                        <?php

                                        // 5 stars based on like and dislike
                                        $like = $scan['like'];
                                        $dislike = $scan['dislike'];
                                        $total = $like + $dislike;

                                        if ($total > 0) {
                                            $ratio = $like / $total;
                                            $note = round($ratio * 5, 1);
                                        } else {
                                            $note = 0;
                                        }


                                        ?>
                                        <?= $note ?>/5
                                    </span>
                                </div>
                            </div>
                            <div class="info">
                                <h2><?= htmlspecialchars($scan['name']) ?></h2>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="allSearchEmpty">
                    <img src="/assets/img/mascot/sleeping.png" alt="">
                    <h2>No scans found</h2>
                    <?php if (!empty($searchTerm)): ?>
                        <p>Try a different search term or <a href="/all">browse all scans</a></p>
                    <?php else: ?>
                        <p>No scans available yet</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- TODO : make styles for the pagination -->

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?<?= !empty($searchTerm) ? 'q=' . urlencode($searchTerm) . '&' : '' ?>page=<?= $page - 1 ?>" class="pagination__prev">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                                <path d="M560-240 320-480l240-240 56 56-184 184 184 184-56 56Z" />
                            </svg>
                            Previous
                        </a>
                    <?php endif; ?>

                    <div class="pagination__numbers">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <?php if ($i == 1 || $i == $totalPages || ($i >= $page - 2 && $i <= $page + 2)): ?>
                                <a href="?<?= !empty($searchTerm) ? 'q=' . urlencode($searchTerm) . '&' : '' ?>page=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>">
                                    <?= $i ?>
                                </a>
                            <?php elseif ($i == $page - 3 || $i == $page + 3): ?>
                                <span class="pagination__ellipsis">...</span>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </div>

                    <?php if ($page < $totalPages): ?>
                        <a href="?<?= !empty($searchTerm) ? 'q=' . urlencode($searchTerm) . '&' : '' ?>page=<?= $page + 1 ?>" class="pagination__next">
                            Next
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                                <path d="M504-480 320-664l56-56 240 240-240 240-56-56 184-184Z" />
                            </svg>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        </div>

    </main>

</body>

</html>