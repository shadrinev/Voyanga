rm Makefile
rm -rf ./frontend/www/assets
mkdir ./frontend/www/assets
chmod 777 ./frontend/www/assets
curl -L http://frontend.voyanga.lcl/deploy.php
make