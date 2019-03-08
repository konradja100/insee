# insee

Steps to reporoduce:

git clone https://github.com/konradja100/insee.git

run composer update

clone .env.dist > .env

Add your github personal API key to .env

Command example:
php bin/console find:sha konradja100/insee master
