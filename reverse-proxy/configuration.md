# Run reverse proxy to host multiple apps on subdomains
## Tutorial link
https://linuxhandbook.com/nginx-reverse-proxy-docker/

## Run nginx-proxy container and certificate helper container:

* Create new network
$ docker network create reverse-proxy-net

* Start your reverse proxy
$ docker-compose up -d

## Start your containers with a VIRTUAL_HOST and LETSENCRYPT_HOST environment variables:

* Example to see if works
$ docker run --rm --name nginx-dummy -e VIRTUAL_HOST=test.localhost -e LETSENCRYPT_HOST=test.localhost -e VIRTUAL_PORT=80 --network reverse-proxy-net -d nginx:latest

* In general
$ docker run --rm --name my_app -e VIRTUAL_HOST=sub.domain.com -e LETSENCRYPT_HOST=sub.domain.com -e VIRTUAL_PORT=80 --network reverse-proxy-net -d my_image

## Run whole apps with docker-compose
* Run in background
$ docker-compose up -d
* For example setups with reverse-proxy, see the docker-compose.yml files in the respective sub-folders
* Example in memorygrid folder