#!/bin/bash

yes_im_sure="n"

php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear

while getopts ":f:" opt; do
  case $opt in
    f) yes_im_sure="$OPTARG"
    ;;
    \?) echo "Invalid option -$OPTARG" >&2
    ;;
  esac
done

if [ $yes_im_sure = "n" ]; then
echo -n "Are you sure? This will wipe all databases (Cannot be recovered! (y/N)";
read yes_im_sure
fi

if [[ "$yes_im_sure" = "y" ]] || [[ "$yes_im_sure" = "Y" ]]; then

	# Remove old logs
	echo "Removing old logs";
	rm storage\logs\*.log -f

	# Empty existing databases
	echo "Wiping databases";
	php artisan db:wipe -v

	# Create basic database
	echo "Creating databases";
	php artisan migrate:fresh --force -v

	# Run base seeder
	echo "Running base seeders";
	php artisan db:seed --force -v
	php artisan db:seed --class=RefreshDataSeeder --force -v

else
	echo "Refresh aborted";
	php artisan migrate
	php artisan migrate --env=testing
fi