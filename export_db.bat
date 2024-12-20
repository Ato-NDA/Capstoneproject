@echo off
"C:\xampp\mysql\bin\mysqldump.exe" -u root camera_rental > database.sql
echo Database exported to database.sql
pause
