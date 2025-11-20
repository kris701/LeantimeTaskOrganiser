docker exec -u root docker-leantime-leantime-1 sh -c "rm -r /var/www/html/app/Plugins/TaskOrganiser"
docker cp ./TaskOrganiser docker-leantime-leantime-1:/var/www/html/app/Plugins/
docker restart docker-leantime-leantime-1
echo "Done!"