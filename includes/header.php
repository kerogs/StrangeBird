    <header>
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
                    <?php if (!isset($_COOKIE['user_id'])) { ?>
                        <a href="/login">
                            <li class="icon_button">
                                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                                    <path d="M480-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM160-160v-112q0-34 17.5-62.5T224-378q62-31 126-46.5T480-440q66 0 130 15.5T736-378q29 15 46.5 43.5T800-272v112H160Z" />
                                </svg>
                            </li>
                        </a>
                    <?php } ?>
                </ul>
            </nav>
        </div>
    </header>