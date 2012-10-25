Version 0.1.4 fixed an issue related to storing date-time values in MySQL databases. See 
http://conjoon.org/issues/browse/CN-396
It introduced a BC-break by starting to convert specific dates to the UTC time zone before saving
them into the database.
The patch found within this folder will convert all available related data of previous versions of
conjoon to the UTC time zone. See also http://conjoon.org/issues/browse/CN-405