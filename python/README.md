## How to install python application

- Create virtualenv
- Create .env and fill with environment variables
- Install dependencies (Ubuntu)
    #### Python 3
    `sudo apt-get install python3 python-dev python3-dev \
         build-essential libssl-dev libffi-dev \
         libxml2-dev libxslt1-dev zlib1g-dev \
         python-pip`

    #### Python 2
    `sudo apt-get install python-dev  \
         build-essential libssl-dev libffi-dev \
         libxml2-dev libxslt1-dev zlib1g-dev \
         python-pip`

    #### General
    
    `sudo apt-get install unixodbc-dev`

- `pip install python/requirements.txt`

## How to link my google ads account to adevolver?

- Run the php/vue application and go to **/register** url and perform the activation process.

    When you first register the app will download accounts associated with your user and add them to the accounts 
    table. It will also store the refresh token in the users table. 

    If the accounts don’t download you may need to run `every_time_user_registers.py` manually.

## How to test python application

1. Downloads google ads accounts using: `python every_time_user_registers.py` (this will run automatically if the queue is running - `php artisan queue:work`)
2. Create a record on mutations table
3. Downloads campaigns and more: `python every_night.py -a your-test-account-uuid`
4. Execute mutations using: `python every_ten_seconds.py`
5. Check mutations (batch jobs) using: `python every_ten_seconds.py`

**Note**: For perform real changes on google ads, `APP_DEBUG=false` (.env file) must be set to false.

## Running unit tests

`python -m unittest discover python`

## Concepts

### Operation (Operation.py)

This class builds the operation for the various mutations we currently support. They are
 - Keyword status
 - Keyword bid
 - Ad status
 - Campaign status
 - Expanded Text Ad creation

It's initialised with:
- **row**: The row of data from the table (the class handles one row at a time)
- **service_type**: One of keyword, advert, campaign
- **action**: Add or set (create something new or update the entity)
- **attribute**: The attribute we're updating (optional as this isn't required if the entity is being created)

So with this class you can pretty much pass a row from the mutations table and it will (via the get method) return an operation ready for the AdWords API.

### Worker (Worker.py)

This is the main class which reads from the mutations table, creates the operation (from the above Operation class), makes the request and handles the response.
The actual mutation request is sent via the Update class within `python/api/mutations_queue`.

### Update (Update.py)

As above, this class takes the operation and actually sends the request.
Using batch job service or regular mutations.
