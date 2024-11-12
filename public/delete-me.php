<?php

// ! In the top right-hand corner of the web page, you'll find a button which,
// ! once clicked, will automatically delete everything and clean up all the 
// !necessary files.




if (isset($_GET['action']) && $_GET['action'] == 'clean') {



    // ! CSS/SCSS
    $default_scss_content = "
@import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300..700&display=swap');

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}
";

    file_put_contents("src/scss/style.scss", $default_scss_content);
    file_put_contents("src/css/style.css", $default_scss_content);
    unlink("src/css/style.css.map");

    // ! remove JS
    $default_ts_content = 'console.log("main -> OK");';
    file_put_contents("src/ts/main.ts", $default_ts_content);
    file_put_contents("src/dist/local/js/main.js", $default_ts_content);

    // ! remove all images
    $images = glob("src/img/*");
    foreach ($images as $image) {
        unlink($image);
    }
    file_put_contents("src/img/.gitkeep", "");

    // ! remove this file
    unlink('delete-me.php');

    header('location: /');
    exit();
}

?>

<nav>
    <div class="logo">
        <img src="src/img/logo.png" alt="logo">
    </div>
    <hr>
    <ul>
        <li class="active"><i class='bx bx-book-alt'></i></li>
        <li><a href="https://github.com/KSLaboratories/KerogsPHP-Framework"><i class='bx bx-donate-heart'></i></a></li>
    </ul>
</nav>

<main>
    <header>
        <ul>
            <li>v<?= $kpf_config["framework"]["framework_version"] ?> <i class='bx bxl-github'></i></li>
            <a style="text-decoration: none; color:blue;" href="?action=clean">
                <li class="clean_code">remove preview code</li>
            </a>
        </ul>
    </header>

    <div class="container">
        <div class="container__title">
            <h1>Welcome to <span><?= $kpf_config['framework']['title'] ?></span> 👋</h1>
            <p>Follow the steps below to get you started.</p>
        </div>

        <div class="container__card">
            <div class="subtitle">
                <h2>Lets get started !</h2>
                <h2><span id="clickedCard">?</span>/<span id="totalCard">?</span></h2>
            </div>
            <div class="bar">
                <div id="statusBar"></div>
            </div>

            <!-- cards -->
            <div class="objectif">

                <div class="objectif__card" id="congratulations">
                    <div class="icon">
                        <i class='bx bx-happy-beaming'></i>
                    </div>
                    <div class="name">
                        <h3>Congratulations</h3>
                        <p>Congratulations, KerogsPHP framework has been installed.</p>
                    </div>
                </div>

                <div class="objectif__card" id="structure-file">
                    <div class="icon">
                        <i class='bx bx-sitemap'></i>
                    </div>
                    <div class="name">
                        <h3>structure of files</h3>
                        <p>Discover the framework's file tree.</p>
                    </div>
                </div>

                <div class="objectif__card" id="framework-basics">
                    <div class="icon">
                        <i class='bx bx-package'></i>
                    </div>
                    <div class="name">
                        <h3>Framework basics</h3>
                        <p>Learn the basics of the framework and how to use it.</p>
                    </div>
                </div>

                <div class="objectif__card" id="npm-and-composer">
                    <div class="icon">
                        <i class='bx bxs-component'></i>
                    </div>
                    <div class="name">
                        <h3>Using NPM and Composer.</h3>
                        <p>Learn how to add new NPM packages and Composer dependencies with this short tutorial.</p>
                    </div>
                </div>

                <div class="objectif__card" id="sass">
                    <div class="icon">
                        <i class='bx bxl-sass'></i>
                    </div>
                    <div class="name">
                        <h3>How to use SASS/SCSS</h3>
                        <p>Learn how to use SASS and SCSS with the framework </p>
                    </div>
                </div>

                <div class="objectif__card" id="typescript">
                    <div class="icon">
                        <i class='bx bxl-typescript'></i>
                    </div>
                    <div class="name">
                        <h3>How to use TypeScript</h3>
                        <p>Learn how to use TypeScript with the framework.</p>
                    </div>
                </div>

                <!-- cards end -->

            </div>

        </div>
    </div>

    <div class="blurbck"></div>

    <!-- template -->
    <div class="cardobj" data-objectif="">
        <h2><i class='bx bx-sitemap'></i> template <button class="cardObj__close"><i class='bx bx-x'></i></button></h2>
        <hr>
        <div class="cardobj__content">
        </div>
    </div>

    <!-- congratulation -->
    <div class="cardobj" data-objectif="congratulations">
        <h2><i class='bx bx-happy-beaming'></i> Congratulation <button class="cardObj__close"><i
                    class='bx bx-x'></i></button></h2>
        <hr>
        <div class="cardobj__content">
            <h2>Thank you for installing KerogsPHP</h2>
            <p>
                Thank you for installing KerogsPHP Framwork. Our aim is to make your life easier when creating small or
                medium-sized projects. We also include a number of tools to help you with development.
            </p>


            <h2>What's next?</h2>
            <p>
                Now that you've successfully installed the KerogsPHP Framework on your server, you have 2 options.
            </p>

            <div class="note-tip">
                <h3>It's my first time, so I'm a bit lost 👉👈</h3>
                <p>We advise you to follow the next steps, which will show you how to use the full power of KerogsPHP
                    Framework.</p>
            </div>

            <div class="note-tip">
                <h3>It's not my first time, I know what to do 😎</h3>
                <p>If you've already used KerogsPHP Framework via an earlier version or the same version, you can simply
                    program your website directly.</p>
            </div>

            <h2>Contribution</h2>
            <p>
                If you'd like to contribute to the development of the KerogsPHP Framework, we invite you to clone the
                repository and make your own improvements and PR on Github.
            </p>
        </div>

    </div>

    <!-- structure-file -->
    <div class="cardobj" data-objectif="structure-file">
        <h2><i class='bx bx-sitemap'></i> Structure of files <button class="cardObj__close"><i class='bx bx-x'></i></button>
        </h2>
        <hr>
        <div class="cardobj__content">
            <h2>Here's how KerogsPHP is structured.</h2>
            <pre>
📦KerogsPHP-Framework
┣ 📂.ksinf
┃ ┗ 📂.ignore
┣ 📂api
┣ 📂backend
┃ ┣ 📂data
┃ ┣ 📂func
┃ ┣ 📜core.php
┃ ┗ 📜core-labs.php
┣ 📂inc
┣ 📂public
┃ ┣ 📂error
┃ ┣ 📂src
┃ ┃ ┣ 📂css
┃ ┃ ┣ 📂html
┃ ┃ ┣ 📂img
┃ ┃ ┣ 📂js
┃ ┃ ┣ 📂scss
┃ ┃ ┣ 📂ts
┃ ┃ ┗ 📂xml
┃ ┣ 📜.htaccess
┃ ┣ 📜index.php
┃ ┗ 📜package.json
┣ 📂test
┃ ┣ 📂src
┃ ┗ 📜index.php
┣ 📜.gitignore
┣ 📜.htaccess
┣ 📜composer.json
┣ 📜config.php
┣ 📜config.yml
┣ 📜LICENSE
┣ 📜README.md
┗ 📜robots.txt
</pre>

            <h2>Folder and file details</h2>

            <h2>Details of Folders and Files</h2>
            <ul>
                <li><code>.ksinf</code> : a special folder that's created for you to put files and such that won't be used
                    by the site.</li>
                <li><code>api</code> : Folder for API scripts</li>
                <li>
                    <code>backend</code> : Contains backend files, divided into subfolders:
                    <ul>
                        <li><code>data</code> : For application-specific data.</li>
                        <li><code>func</code> : For backend functions.</li>
                        <li><code>core.php and core-labs.php</code> : Main files for the backend core.</li>
                    </ul>
                </li>
                <li><code>inc</code> : Contains inclusions, such as HTML head configuration files for SEO or header, nav,
                    footer...</li>
                <li>
                    <code>public</code> : Publicly accessible folder, with the following subfolders and files:
                    <ul>
                        <li><code>error</code> : Custom error pages (403, 404, etc.).</li>
                        <li>
                            <code>src</code> : Front-end resources such as CSS, HTML, images, JavaScript, SCSS, TypeScript,
                            and XML. Includes the sitemap configuration file (sitemap.xml).
                            <ul>
                                <li><code>.htaccess</code> : Configuration file for Apache.</li>
                                <li><code>index.php</code> : Main entry point of the application.</li>
                                <li><code>package.json</code> : Configuration file for npm.</li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <li><code>test</code> : Test section accessible via the URL test.(domain).(extension), e.g.,
                    test.ks-infinite.fr.</li>
                <li><code>.gitignore</code> : Configuration file for Git to ignore certain files during commits.</li>
                <li><code>composer.json</code> : Configuration file for Composer.</li>
                <li><code>config.php and config.yml</code> : Application configuration files.</li>
                <li><code>LICENSE and README.md</code> : Information about the license and documentation.</li>
                <li><code>robots.txt</code> : Configured file for indexing robots.</li>
            </ul>


        </div>
    </div>

    <!-- framework-basics -->
    <div class="cardobj" data-objectif="framework-basics">
        <h2><i class='bx bx-package'></i> template <button class="cardObj__close"><i class='bx bx-x'></i></button></h2>
        <hr>
        <div class="cardobj__content">
            <h2>Configuring your application</h2>
            <h3>config.yml</h3>
            <p>To configure your application correctly, you'll first need to modify the basic content present in the
                <code>/config.yml</code> file. This will enable you to modify the basic SEO information for your site and
                other important information.
            </p>
            <pre>
# KerogsPHP Framework Configuration
# https://github.com/KSInfinite/KerogsPHP-Framework
# Thanks for using it.

# SEO Configuration
seo:
title: "KerogsPHP Framework"
title_short: "KsPHP Framework"
description: "KerogsPHP Framework. The framework for all your development needs."
keywords: "KerogsPHP, PHP, Framework, website, html, css, easy, best, KerogsPHP Framework, kpf"
author: "Kerogs"
lang: "English"
lang_short: "en"
color: "#a038ec"
image: "https://raw.githubusercontent.com/KSInfinite/KerogsPHP-Framework/main/.ksinf/banner.png"
favicon: "src/img/logo.png"
sitemap: "src/xml/sitemap.xml"

# Labs/test IP authorization Configuration
labs:
enable: false
labsIPAuth:
- 127.0.0.1

# Other
other:
website_version: 1.0.0

framework:
framework_version: 2.1.2
title: "KerogsPHP Framework"
title_short: "KsPHP Framework"</pre>
            <p>you can retrieve all the information written there using the following PHP variable :
                <code>$kpf_config['val1']['val2']</code>
            </p>
            <h3>PWA configuration</h3>
            <p>Here is the list of file paths for the PWA</p>
            <ul>
                <li><code>/public/sw.js</code></li>
                <li><code>/public/manifest.json</code></li>
                <li><code>/public/pwa/*</code> <- All PWA files here</li>
            </ul>
            <p>If you're not using PWA, you can just delete the following files/folders. Otherwise, just replace the
                information in <code>/public/manifest.json</code> with the following</p>
            <p>content of <code>manifest.json</code> file</p>
            <pre>
{
"short_name": "KerogsPHP",
"name": "KerogsPHP Framework",
"description": "KerogsPHP Framework. The framework for all your development needs.",
"start_url": "/index.php",
"theme_color": "#a038ec",
"background_color": "#F4F4F9",
"Author": "KS Infinite",
"version": "2.1.2",
"icons": [
    {
        "src": "src/img/favicon.ico",
        "type": "image/x-icon",
        "sizes": "16x16 32x32"
    },
    {
        "src": "src/img/icon-192.png",
        "type": "image/png",
        "sizes": "192x192"
    },
    {
        "src": "src/img/icon-512.png",
        "type": "image/png",
        "sizes": "512x512"
    },
    {
        "src": "src/img/icon-192-maskable.png",
        "type": "image/png",
        "sizes": "192x192",
        "purpose": "maskable"
    },
    {
        "src": "src/img/icon-512-maskable.png",
        "type": "image/png",
        "sizes": "512x512",
        "purpose": "maskable"
    }
],
"display": "standalone",
"screenshots": [
    {
        "src": "pwa/src/img/preview1.png",
        "sizes": "586x1041",
        "type": "image/png"
    },
    {
        "src": "pwa/src/img/preview3.png",
        "sizes": "586x1041",
        "type": "image/png"
    },
    {
        "src": "pwa/src/img/preview1.png",
        "sizes": "586x1041",
        "type": "image/png"
    }
],
"lang": "en",
"orientation": "portrait"
}          
    </pre>
        </div>
    </div>

    <!-- npm-and-composer -->
    <div class="cardobj" data-objectif="npm-and-composer">
        <h2><i class='bx bxs-component'></i> template <button class="cardObj__close"><i class='bx bx-x'></i></button></h2>
        <hr>
        <div class="cardobj__content">
            <h2>Composer</h2>
            <div class="note-tip">
                <h3>Composer required</h3>
                <p>To simplify your life and improve security, you must have composer installed on your machine. If you
                    don't have it, our RTU version of the framework is available for you.</p>
            </div>
            <ol>
                <li>Install composer with the following command in <code>/</code></li>
                <pre>composer install</pre>
            </ol>
            <h2>NPM</h2>
            <p>If you wish to use NPM, you must perform the following action: </p>
            <ol>
                <li>Go in the <code>/public</code> folder</li>
                <pre>cd ./public</pre>
            </ol>
            <ol>
                <li>Install NPM with the following command</li>
                <pre>npm install</pre>
            </ol>
        </div>
    </div>

    <!-- sass -->
    <div class="cardobj" data-objectif="sass">
        <h2><i class='bx bxl-sass'></i> SASS <button class="cardObj__close"><i class='bx bx-x'></i></button></h2>
        <hr>
        <div class="cardobj__content">
            <h2>SASS</h2>
            <p><code>.sass</code> files are compiled to <code>.css</code> files in the <code>/public/src/css</code>

            <p>If you're using SCSS and not SASS, you can always move the files to SASS. This won't change anything, the
                folder simply serves as a separator with the .css compilation. By the way, .css.map files are also moved
                into the .css folder. If you have any preferences, you can always do it your way.
                <br><br>
                The use of the SASS language is by no means compulsory. You can also delete the SASS folder itself if you
                wish.
            </p>

            <h2>Auto compiler</h2>
            <h3>Live Sass Compiler</h3>
            <p>
                If you wish to use a compiler for SASS, the tutorial will be ported to the : <b><em>Live Sass Compiler</em></b>
                created
                by
                <b><em>Glenn Marks</em></b> (<a href="https://marketplace.visualstudio.com/items?itemName=glenn2223.live-sass"
                    target="_blank">see by clicking here</a>)
            </p>
            <h4>Configuration</h4>
            <p>
                You can configure the output of the sass file by going to “extensions settings” then “edit in settings.json” and adding the following line, which will automatically configure the CSS output of SASS files after compilation.
            </p>
            <pre>
"liveSassCompile.settings.formats": [{
"format": "expanded",
"extensionName": ".css",
"savePath": "~/../css/"
}
    </pre>
            <p>This configuration does the following:</p>
            <ul>
                <li><code>~/</code> allows access to the folder where the .sass file is located</li>
                <li><code>../</code> allows you to go back one step</li>
                <li><code>css/</code> allows you to create a folder (if it doesn't exist) and drop all compiled .css files into the CSS folder</li>
            </ul>
            <p>The result is as follows: </p>
            <pre>~/../css/</pre>
        </div>
    </div>

    <!-- typescript -->
    <div class="cardobj" data-objectif="typescript">
        <h2><i class='bx bxl-typescript'></i> Typescript <button class="cardObj__close"><i class='bx bx-x'></i></button></h2>
        <hr>
        <div class="cardobj__content">
            <h2>Typescript</h2>
            <p>The typescript files are compiled to <code>.js</code> files in the <code>/public/src/js</code></p>
            <p>Be sure to install the typescript compiler. with the following command in <code>npm i</code> (normaly already done with the step <b><em>Using NPM and Composer</em></b> )</p>
            <pre>npm i</pre>
            <p>If you wish to use Typescript, you need to type this in the terminal (at the root of the project)</p>
            <pre>npx tsc --watch</pre>
            <h3>Default tsconfig.json code</h3>
            <pre>
            {
                "compilerOptions": {
                    "outDir": "public/src/js/",
                    "noEmitOnError": true,
                    "target": "ES6"
                },
                "include": [
                    "public/src/ts/**/*.ts",
                    "public/src/ts/*.ts",
                ]
            }
    </pre>
        </div>
    </div>

</main>
<script src="dist/local/js/main.js"></script>