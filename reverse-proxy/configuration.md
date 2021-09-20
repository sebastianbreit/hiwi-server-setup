# Run reverse proxy to host multiple apps on subdomains
## Tutorial link
https://linuxhandbook.com/nginx-reverse-proxy-docker/

## Run nginx-proxy container and certificate helper container:

$ docker-compose up -d

## Start your containers with a VIRTUAL_HOST and LETSENCRYPT_HOST environment variables:

$ docker run --rm --name my_app -e VIRTUAL_HOST=sub.domain.com -e LETSENCRYPT_HOST=sub.domain.com -e VIRTUAL_PORT=80 --network net -d my_image

