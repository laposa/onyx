# do not edit SQL template files directly
# example for ecommerce_uk.sql when upgrade from 1.7 to 1.8 before release

username="db_user"
hostname="localhost"
database_name="onxshop_template_temp"
onxshop_directory="/opt/onxshop/dev"
template_filename="ecommerce_uk.sql"
upgrade_filename="upgrade-1.7.x-to-1.8.0.sql"

createdb -U $username -h $hostname -E UTF8 $database_name
psql -U $username -h $hostname $database_name < $onxshop_directory/project_skeleton/$template_filename
psql -U $username -h $hostname $database_name < $onxshop_directory/docs/database/$upgrade_filename
pg_dump -U $username -h $hostname --no-owner $database_name > $onxshop_directory/project_skeleton/template_filename
dropdb -U $username -h $hostname $database_name

# do the same for all templates in project_skeleton folder

