#!/bin/bash -e
# do not edit SQL template files directly
# example for base.sql when upgrade from 1.7 to 1.8 before release

db_username="onyx"
db_password=""
hostname="localhost"
temp_database_name="onyx_template_temp"
onyx_directory="/opt/onyx/dev"
template_filename="base.sql"
upgrade_filename="upgrade-1.7.x-to-1.8.0.sql"

export PGHOST=$hostname
export PGUSER=$db_username
export PGPASSWORD=$db_password

createdb -E UTF8 $temp_database_name
psql $temp_database_name < $onyx_directory/project_skeleton/$template_filename
psql $temp_database_name < $onyx_directory/docs/database/$upgrade_filename
pg_dump --no-owner $temp_database_name  > $onyx_directory/project_skeleton/$template_filename
dropdb $temp_database_name 

# do the same for all templates in project_skeleton folder

