Working with migrations
=======================

1. Try not to alter your database directly and always do it with migrations.
This will allow you to be sure your DB is state is the same as on the server.
2. Committed migration should be able to be applied, otherwise it will not work
on live server.  If it does not migrate properly you should fix migration
in the same file.
3. If you've committed migration and it is able to be applied don't edit
it further. Create another migration.



