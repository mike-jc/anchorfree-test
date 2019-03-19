## Task ##

Imagine that you have an application with millions of users. Performance is key.
You need to create a backend for it which will handle the following two requests.
The backend has a database which keeps counters for each day, country and event.
Event can be any of "view", "play" or "click"
E.g.

```
2017-07-01 US views 50000
2017-07-01 US plays 100
2017-07-02 US views 3000
2017-07-01 CA clicks 123
...
```

1. Receive data from application. The data is sent by POST. The data is formatted in json.
The backend needs to decode this data and extract the "country" and "event" fields.
Then the backend needs to increment a counter in the database for the current day
for the respective country and event.

2. The application does a GET request. Data should be returned in different formats (json,csv)
according to the request parameters. The response should contain the sum of each event
over the last 7 days by country for the top 5 countries of all times. 

Notes #1:
1. Use only pure PHP. Do not use any framework
2. The table will eventually hold millions of rows and the api will get dozens of requests per second. Returning 100% up2date information in responses is not a requirement but fast responses are.

Notes #2:

1. Please push your code to your Github account
2. This task could be implemented using different approaches and services. You need to write PHP implementation only for one of them. But please describe other ways for solving this task (just ideas)

## Some not-implemented ideas ##

* Use something based on event loop instead of stateless aproach (e.g. ReactPHP) and keep all data for the last 7 days in memory. Or use some kind of "in-memory" engine for database that exists in MySQL for example.
* Keep all data and use partitioning of it by date. For example, one table for one month.
* Asynchronous responses on POST requests. We can respond with HTTP 200 immediately and then update counter. To implement this we can use threads or asynchronous cURL functions (http://php.net/manual/en/function.curl-multi-init.php) 
* If suddenly there will be new condition to give summary for a long period instead of 7 days (e.g. 5 years), it makes sense to have a separate table where we can store the summary for the all days in the requested period excluding the current day. Let's say it's `pre-summary` table. So for 5 years we would store summary for all countries for 5*365-1 days. When the first GET request will arrive today we will update `pre-summary` table and then only add current day's data to it on each next GET request.

## Current implementation ##

* Fix-sized pool of data. On the first POST request of the new day we delete the oldest day's data from the table to keep only N last days in it. Table is always quite small - quick updates on POST and quick selects on GET.
* Separate table to keep country rating over all time without keeping all data and without recalculating top countries on each request.
* Use scheduled jobs for some daily data processing