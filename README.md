# D8-sandbox
Example Task
1.	Create a custom content entity called “Customer” that has three fields:

    1.1 Customer id (integer)
    1.2 Customer name (textfield)
    1.3 Balance (float)

2.	Create a view that shows a list of “Customers” ordered by customer id.
3.	Create a custom page with its own route that renders results of “Customers list” view described above. Add the ability to filter a view by customer id using argument from a page route.
4.	Create a CSV parser that would create/update Customer entities from the .csv file by a cron task every hour.
5.	Expose newly created entity as REST resource, so that it's possible to CREATE, GET, POST, PATCH and DELETE "Customer" entities.
