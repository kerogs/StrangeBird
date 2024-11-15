<div align="center">
    <img alt="Logo" src=".ksinf/pyhwa.svg" height="120">
    <h3>PyHwa</h3>
    <p><em>📜 PHP scanning site for manga, manhwa, manhua with login system. A complete site without database and automation (second version of PyHwa).</em></p>
</div>

# PyHwa2

> [!NOTE]
> the first account created will automatically have full rights and supreme permission.

## 🔧 Features
- [ ] Permission system (owner, admin, publisher, member...)
- [ ] Change nickname
- [ ] customizable profile page (banner, background, what to display, description, ...)
- [x] nickname decoration (glitter, glow, icon, custom colors, title)
- [ ] profile photo decoration
- [x] change profile photo (jpg, png, gif)
- [ ] statistics (what's been read, bookmarked)
- [ ] track what people are reading
- [ ] notification system
- [ ] announcement
- [ ] custom discord link
- [ ] several themes
- [x] no database
- [x] login/registration page
- [ ] custom page to publish
- [ ] automatic retrieval of metadata (from mangadex, anilist...)
- [ ] Comment system

## Docker build && run
```sh
docker compose up --build
```

## data storage
everything works without a database. So below you'll find out how certain elements work.

### accounts
Accounts are created in the folder ``backend/storage/accounts/{uuid}.json``. Each account will have a unique uuid and a unique username.

Also, each time an account is created or the unique username is modified, the value will be added/modified in the ``backend/storage.json`` file.

This allows multiple accounts to be registered. The nameid allows the nickname to be displayed without any conflict between people with the same username. For example, in the user profile url. This allows you to have a url that doesn't use the user's uuid. 

To avoid the server having to search for the user in every folder, the storage.json file is used to link the uuid to the user's unique name.