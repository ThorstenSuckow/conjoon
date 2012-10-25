Version 0.1.5 fixed an issue related to storing multibyte characters in a MySQL
database.
The patch found within this folder will convert all available multibyte strings
from all tables to properly encoded utf-8 values.