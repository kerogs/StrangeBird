<?php

// Pages with a transparent header and be blur when scrolling
$shyHeader = ['index.php', 'scan.php'];
$angryHeader = ['read.php']

/*
class="<?= in_array(basename($_SERVER['PHP_SELF']), $shyHeader) ? 'header-shy' : ''; ?>"
*/

?>
<header class="<?= in_array(basename($_SERVER['PHP_SELF']), $shyHeader) ? 'header-shy' : ''; ?> <?= in_array(basename($_SERVER['PHP_SELF']), $angryHeader) ? 'header-angry' : ''; ?>">
    <div class="header__inner">
        <nav>
            <ul>
                <a href="/">
                    <li class="logo">
                        <img src="/assets/img/logo_primary.png" alt="">
                    </li>
                </a>

                <form action="all" class="searchHeader" method="GET">
                    <div class="group">
                        <input type="text" name="q" id="q" placeholder="Search...">
                        <button type="submit">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                                <path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z" />
                            </svg>
                        </button>
                    </div>
                </form>
            </ul>
        </nav>
        <nav>
            <ul>
                <a href="">
                    <li class="icon_button">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                            <path d="M520-120v-320h320v320H520Zm0-400v-320h320v320H520Zm-400 0v-320h320v320H120Zm0 400v-320h320v320H120Z" />
                        </svg>
                    </li>
                </a>
                <?php if (!$auth->isLoggedIn()) { ?>
                    <a href="/login">
                        <li class="icon_button">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                                <path d="M480-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM160-160v-112q0-34 17.5-62.5T224-378q62-31 126-46.5T480-440q66 0 130 15.5T736-378q29 15 46.5 43.5T800-272v112H160Z" />
                            </svg>
                        </li>
                    </a>
                <?php } elseif ($auth->isLoggedIn()) { ?>
                    <a href="/add/scan">
                        <li class="icon_button">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                                <path d="M440-280h80v-160h160v-80H520v-160h-80v160H280v80h160v160Zm40 200q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Z" />
                            </svg>
                        </li>
                    </a>
                    <a href="/profile/<?php echo htmlspecialchars($auth->getUser()['id']); ?>">
                        <li class="profile">
                            <img src="<?php echo htmlspecialchars($auth->getUser()['profile_picture']); ?>" alt="">
                        </li>
                    </a>
                <?php } ?>
            </ul>
        </nav>
    </div>
</header>

<?php if (in_array(basename($_SERVER['PHP_SELF']), $shyHeader)) { ?>
    <script>
        const header = document.querySelector('header');

        window.addEventListener('scroll', () => {
            if (window.scrollY > 10) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
    </script>
<?php } ?>