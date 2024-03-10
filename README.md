## Initial start-up
1. ```bash
    composer install
   ```
2. ```bash
    npm install
   ```
3. create .env file (database is configured)
4. ```bash
    docker compose build
   ```
5. Run container
      ```bash
        docker compose up
     ```   

## If you want to use the "artisan" command,
1. Open a new console
2. Type
   ```bash
   alias sail='sh $([ -f sail ] && echo sail || echo vendor/bin/sail)'
    ```
   
3. Now release the migration:
    ```bash
    sail artisan migrate:refresh --seed
    ```

Run all artisan commands using the
`sail artisan ...`

## How to work with react
Docker is configured for production
Therefore, in order to work on dev (to see changes in real time), enter the directory `cd react/`


and there run 
```bash
npm run dev
```

Now under the url `http://localhost:3000/` see the react application on which you can work

## Default Account
Email: `user@example.com`

Password: `user`

## How to connect to the database
Host: `localhost`

Port: `3355`

Username: `sail`

Password: `password`

Database: `example_app`

## How to open applications

Laravel: http://localhost:215/

React: http://localhost:8999/
