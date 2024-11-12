# PyHwa2
📜 PHP website for scan manga, manhwa, manhua with login system. (second version of PyHwa)

## data storage
everything works without a database. So below you'll find out how certain elements work.

### accounts
Accounts are created in the folder ``backend/storage/accounts/{uuid}.json``. Each account will have a unique uuid and a unique username.

Also, each time an account is created or the unique username is modified, the value will be added/modified in the ``backend/storage.json`` file.

This allows multiple accounts to be registered. The nameid allows the nickname to be displayed without any conflict between people with the same username. For example, in the user profile url. This allows you to have a url that doesn't use the user's uuid. 

To avoid the server having to search for the user in every folder, the storage.json file is used to link the uuid to the user's unique name.