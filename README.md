# StrangeBird


<div align="center">
    <img alt="Logo" src="assets/img/favicon/favicon.svg" height="120">
    <h3>StrangeBird</h3>
    <p><em>A self-hosted solution for reading manhwa, manga, and manhua.</em></p>
</div>

This is the second version — currently a prototype. [Here the first version](https://github.com/kerogs/PyHwa)

## Important Information
- Anyone can add, modify, or delete scans. An admin system is not implemented yet — please be patient.
- Not intended for public use; use at your own risk.
- Please report any bugs or ideas.

## Features
- System for creating and logging into an account
- Choice of advanced reading mode (manga, manhwa, change preferences)
- Synchronisation between multiple devices
- Save, like or dislike scans
- Simplified and advanced search
- Integrated form for adding new scans
- Add chapters one by one or in groups
- Cosy and pleasant design
- Integrated tagging with colours
- More to come...

## Architecture of Batch Additions

> [!NOTE]
> The batch addition system is still in beta and may not function perfectly.

When performing batch additions, it’s important to follow a specific structure.

If you’re using **Hakuneko** to download chapters, most of the preparation is already handled for you.

### Naming Conventions
- Each chapter folder **must** use the format:
  
  `Ch.XXXX`  

  Here, `X` represents the chapter number.

- Inside each chapter folder, images **must** be sequentially numbered starting from `1` up to however many pages are required.

### Packaging
Once all chapter folders are ready:
- Place them inside a single `.zip` archive.
- Upload that `.zip` file.

### Example Directory Structure
```
scan.zip
├── Ch.0001 first chapter/
│   ├── 1.png
│   ├── 2.png
│   └── 3.png
├── Ch.0002 The next chapter/
│   ├── 1.png
│   ├── 2.png
│   └── 3.png
└── Ch.0003 UcanAddSomeInfo (en) [SBScanTrad]/
    ├── 1.png
    ├── 2.png
    └── 3.png
```

## Quick Q&A

<details>
<summary>Can I help? How?</summary>

Absolutely — any help is welcome!  
You can contribute by writing code, reporting bugs, or suggesting improvements. Every bit helps.
</details>

<details>
<summary>Can I add my own manga and chapters?</summary>

Yes.  
The site allows you to upload your own manga and chapters.  
Only the person who originally added a manga/manhwa can currently add chapters to it.  
A permission-sharing system may be added in the future.
</details>

<details>
<summary>I have a lot of chapters to upload — can I add them all at once?</summary>

Yes.  
Check the `README.md` under the section [Architecture of Batch Additions](#architecture-of-batch-additions) for instructions.
</details>

<details>
<summary>Can I resume from my phone and see which chapter I stopped at?</summary>

Yes.  
Your progress is stored in the database and synced across devices when you log in with the same account.
</details>

<details>
<summary>Can I publish the site online?</summary>

Yes, you can host the site wherever you want.  
Keep in mind:
- I’m the sole developer, so security issues may exist.
- The site was originally designed for **local network use**, not public deployment.  

If you put it online, you do so at your own risk.
</details>

<details>
<summary>What can I do with the site?</summary>

Read the LICENSE file for full details.  

If you host the site publicly, please leave a reference back to the repository.  
It helps future contributors and makes me happy too!
</details>

<details>
<summary>Is there an automatic update system built in?</summary>

Unfortunately no.  
There’s currently no automatic update mechanism — you’ll need to install new versions manually.  
I don’t have the time to develop one yet, sorry!
</details>

<details>
<summary>Does it work fully offline?</summary>

Yes — everything is stored locally.  
Your uploaded scans and images remain on your server; nothing depends on the internet.
</details>

<details>
<summary>Is any data sent or collected?</summary>

No.  
All your data stays on your machine.  
I don’t care if you uploaded 30 GB of adult content — nothing leaves your system.
</details>

<details>
<summary>I want to help with programming — what can I do?</summary>

If you have an idea, want to optimize the code, debug, or just improve something, go ahead.  
When making pull requests, please clearly explain what you changed so it’s easy to review.
</details>

<details>
<summary>I use other languages — can I add them?</summary>

Prefer sticking to the existing languages.  

If there’s a good reason to introduce a new language (like Python), that’s fine.  
For TypeScript vs JavaScript, JS is preferred.  
Remember: most users don’t want to set up dozens of runtimes just to run the site.
</details>

<details>
<summary>Are images compressed?</summary>

No.  
Images are stored as-is, so pay attention to their file size before uploading.
</details>

<details>
<summary>Which file formats are allowed?</summary>

Currently allowed formats: **JPG**, **PNG**, **WEBP**.  
GIF may be added in the future if requested.
</details>

<details>
<summary>In which language is the site available?</summary>

Currently only **English**.  
If someone wants to help add multi-language support, contributions are welcome.  
I just haven’t had the motivation to implement it yet.
</details>

<details>
<summary>Can the site automatically download and update my favorite manga/manhwa/chapters?</summary>

No.  
StrangeBird’s goal is simply to make your reading experience pleasant.  
It does **not** support automatic downloads or updates of manga/manhwa.
</details>

<details>
<summary>What do you recommend for downloading my manhwa?</summary>

Use [Hakuneko](https://github.com/manga-download/hakuneko).
</details>

<details>
<summary>Is it legal?</summary>

That depends on your country and what you do with the site.  
You’re responsible for ensuring compliance with your local laws and for the content you upload.
</details>

<details>
<summary>Is StrangeBird available as an online website?</summary>

No.  
I don’t plan to host it online.  
If you want to use the site, install it yourself — preferably locally.
</details>

<details>
<summary>Do I need an account to read?</summary>

No.  
Accounts are only used to like, save, and add chapters/manga/manhwa.  
Remember: everything stays local. Nothing is shared.
</details>

<details>
<summary>Is my account password important?</summary>

It depends:  
- If you’re the only one using the site, it’s not critical.  
- Passwords are hashed in the database, so leaks are unlikely.  

However, don’t use a unique or sensitive password — especially if it’s someone else’s installation.  
You never know what code might be running. Always stay a bit cautious.  

Anyway, if someone gets into your account because you used a weak password… what can they even do?  
Read your scans for you? *lol*
</details>
