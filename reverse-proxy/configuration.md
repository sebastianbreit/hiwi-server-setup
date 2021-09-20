#Run reverse proxy to host multiple apps on subdomains
##Run nginx-proxy container:

$ docker run -d -p 80:80 -v /var/run/docker.sock:/tmp/docker.sock -t jwilder/nginx-proxy

##Start your containers with a VIRTUAL_HOST environment variables:

$ docker run -e VIRTUAL_HOST=foo.bar.com  ...

