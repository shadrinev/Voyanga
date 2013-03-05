rm -f Makefile
rm -rf ./frontend/www/assets
mkdir ./frontend/www/assets
chmod 777 ./frontend/www/assets
curl -u voyanga:rabotakipit -L http://voyanga.com/site/deploy/key/kasdjnfkn24r2wrn2efk > /dev/null
make
ACCESS_TOKEN=856b55e1ff4a41329951cb6f6efb6175
ENVIRONMENT=prod
LOCAL_USERNAME=`whoami`
REVISION=`git log -n 1 --pretty=format:"%H"`

curl https://api.rollbar.com/api/1/deploy/ \
  -F access_token=$ACCESS_TOKEN \
  -F environment=$ENVIRONMENT \
  -F revision=$REVISION \
  -F local_username=$LOCAL_USERNAME