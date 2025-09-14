<?php

require_once __DIR__ . '/includes/core.php';

// ? check if database empty. if yes : $isDatabaseEmpty = true
$stmt = $pdo->query("SELECT * FROM `scan`");
$isDatabaseEmpty = ($stmt->fetch() === false);

$scanRandom = $pdo->query("SELECT * FROM `scan` ORDER BY RANDOM() LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
// var_dump($scanRandom);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StrangeBird | Home</title>

    <link rel="stylesheet" href="/assets/styles/css/style.css">

    <?php require_once __DIR__ . '/includes/head.php' ?>

    <link rel="stylesheet" href="/node_modules/swiper/swiper-bundle.min.css" />
    <script src="/node_modules/swiper/swiper-bundle.min.js"></script>

</head>

<body class="dark">

    <?php require_once __DIR__ . '/includes/header.php' ?>

    <?php if ($isDatabaseEmpty) { ?>

        <div class="noScanMessage">
            <div class="message">
                <center>
                    <img src="/assets/img/mascot/sleeping.png" alt="">
                </center>
                <h2>No scans in the database</h2>
                <?php if ($auth->isLoggedIn()) { ?>
                    <p>It appears that you do not yet have a scan. <a href="/add/scan">Why not add one?</a></p>
                <?php } else { ?>
                    <p>It appears that you do not yet have any scans. <a href="/login">Create or log in to your account</a> to add your first scan.</p>
                <?php } ?>
            </div>
        </div>

    <?php } ?>

    <div class="mainReco-swiper">
        <div class="swiper-wrapper">
            <!-- Slides -->
            <?php foreach ($scanRandom as $scan) { ?>

                <?php

                // ? if background is empty, use cover
                if (empty($scan['background'])) {
                    $scan['background'] = $scan['cover'];
                }

                // ? explode tags into array
                $scan['tag'] = explode(',', $scan['tag']);

                // ? check in database if there are any chapters (preference take the first scan chapter)
                $scan['has_chapters'] = $pdo->prepare('SELECT * FROM "chapters" WHERE id_scan = :id LIMIT 1');
                $scan['has_chapters']->bindValue(':id', $scan['id']);
                $scan['has_chapters']->execute();
                $scan['has_chapters'] = $scan['has_chapters']->fetch(PDO::FETCH_ASSOC);

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

                <div class="swiper-slide">

                    <div class="mainReco" style="background-image:url('<?php echo $scan['background']; ?>');">
                        <div class="mainRecoContent">
                            <div class="filter"></div>

                            <div class="container">
                                <div class="container__inner">
                                    <div class="cover">
                                        <a href="/scan/<?= $scan['id'] ?>">
                                            <img src="<?php echo $scan['cover']; ?>" alt="">
                                        </a>
                                    </div>
                                    <div class="contents">
                                        <h3 data-swiper-parallax="-600"><?php echo $scan['name']; ?></h3>
                                        <p class="tags">

                                            <?php
                                            // if timestamp is less than 7 days, add "new" tag
                                            if (time() - (int)$scan['datetime'] < 60 * 60 * 24 * 7) {
                                                echo '<span class="newscan">New scan</span>';
                                            }
                                            ?>

                                            <?php foreach ($scan['tag'] as $tag): ?>
                                                <?php
                                                $cleanTag = trim(strtolower($tag)); // supprime espaces + normalise en minuscules
                                                $class = $specialTags[$cleanTag] ?? '';
                                                ?>
                                                <span class="<?= $class ?>"><?= htmlspecialchars($tag) ?></span>
                                            <?php endforeach; ?>
                                        </p>
                                        <p class="stats">
                                            <span class="views">
                                                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                                                    <path d="M480-320q75 0 127.5-52.5T660-500q0-75-52.5-127.5T480-680q-75 0-127.5 52.5T300-500q0 75 52.5 127.5T480-320Zm0-72q-45 0-76.5-31.5T372-500q0-45 31.5-76.5T480-608q45 0 76.5 31.5T588-500q0 45-31.5 76.5T480-392Zm0 192q-146 0-266-81.5T40-500q54-137 174-218.5T480-800q146 0 266 81.5T920-500q-54 137-174 218.5T480-200Z" />
                                                </svg>
                                                <?= $scan['view'] ?>
                                            </span>
                                            <span class="stars">
                                                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                                                    <path d="m233-120 65-281L80-590l288-25 112-265 112 265 288 25-218 189 65 281-247-149-247 149Z" />
                                                </svg>
                                                <?= $note ?>/5
                                            </span>
                                        </p>
                                        <p data-swiper-parallax="-900" class="description">
                                            <?php echo $scan['description']; ?>
                                        </p>
                                        <div class="btns">
                                            <a href="/scan/<?= $scan['id'] ?>" class="btn">
                                                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                                                    <path d="M240-280h280v-80H240v80Zm400 0h80v-400h-80v400ZM240-440h280v-80H240v80Zm0-160h280v-80H240v80Zm-80 480q-33 0-56.5-23.5T80-200v-560q0-33 23.5-56.5T160-840h640q33 0 56.5 23.5T880-760v560q0 33-23.5 56.5T800-120H160Z" />
                                                </svg>
                                                See more
                                            </a>
                                            <?php if ($scan['has_chapters']) { ?>
                                                <a href="/scan/<?= $scan['id'] ?>/1" class="btn btn__secondary">
                                                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                                                        <path d="M480-160q-48-38-104-59t-116-21q-42 0-82.5 11T100-198q-21 11-40.5-1T40-234v-482q0-11 5.5-21T62-752q46-24 96-36t102-12q58 0 113.5 15T480-740v484q51-32 107-48t113-16q36 0 70.5 6t69.5 18v-480q15 5 29.5 10.5T898-752q11 5 16.5 15t5.5 21v482q0 23-19.5 35t-40.5 1q-37-20-77.5-31T700-240q-60 0-116 21t-104 59Zm80-200v-380l200-200v400L560-360Zm-160 65v-396q-33-14-68.5-21.5T260-720q-37 0-72 7t-68 21v397q35-13 69.5-19t70.5-6q36 0 70.5 6t69.5 19Zm0 0v-396 396Z" />
                                                    </svg>
                                                    Read now
                                                </a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>


                </div>

            <?php } ?>
        </div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
    </div>

    <script>
        const swiper = new Swiper('.mainReco-swiper', {
            // Optional parameters
            direction: 'horizontal',
            loop: true,
            autoplay: {
                delay: 5000,
                disableOnInteraction: true,
            },
            parallax: true,
            mousewheel: false,
            keyboard: true,
            longSwipesMs: 1000,

            // Navigation arrows
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
        });
    </script>

    <!-- <div class="mainReco" style="background-image:url('/assets/img/templates/cover.webp');">
        <div class="mainRecoContent">
            <div class="filter"></div>

            <div class="container">
                <div class="container__inner">
                    <div class="cover">
                        <a href="">
                            <img src="/assets/img/templates/cover.webp" alt="">
                        </a>
                    </div>
                    <div class="contents">
                        <h3>My bias gets on the last train Lorem, ipsum dolor sit amet consectetur adipisicing elit. Ullam autem consequatur explicabo minima perspiciatis, blanditiis animi est saepe, tenetur quas, earum nulla cum esse laudantium laborum optio molestias excepturi maiores.</h3>
                        <p class="tags">
                            <span>Romance</span>
                            <span>Drama</span>
                            <span>Slice of Life</span>
                            <span>Webtoon</span>
                        </p>
                        <p class="description">
                            “Meeting her on the last train again today, if only I could talk to her!”
                            College student Lee Yeowoon works late and takes the last train every night.
                            Each time, he runs into Shin Haein, a woman carrying a guitar. As if by chance, as if by fate,
                            the two keep meeting and discover that their favorite artist is the indie musician “Long Afternoon.” They gradually grow closer and so the story began. Lorem, ipsum dolor sit amet consectetur adipisicing elit. Quidem neque odio, impedit nostrum illo consequuntur! Exercitationem pariatur repudiandae, nobis sapiente fugiat architecto animi illo amet at optio natus sint eligendi.
                        </p>
                        <div class="btns">
                            <a href="/scan/3" class="btn">
                                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                                    <path d="M240-280h280v-80H240v80Zm400 0h80v-400h-80v400ZM240-440h280v-80H240v80Zm0-160h280v-80H240v80Zm-80 480q-33 0-56.5-23.5T80-200v-560q0-33 23.5-56.5T160-840h640q33 0 56.5 23.5T880-760v560q0 33-23.5 56.5T800-120H160Z" />
                                </svg>
                                See more
                            </a>
                            <a href="/scan/3/1" class="btn btn__secondary">
                                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                                    <path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H200Zm80-160h280v-80H280v80Zm0-160h400v-80H280v80Zm0-160h400v-80H280v80Z" />
                                </svg>
                                Read now
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div> -->

    <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>

</body>

</html>