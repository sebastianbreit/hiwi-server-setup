# Run reverse proxy to host multiple apps on subdomains
## Documentation link
https://github.com/nginx-proxy/nginx-proxy

## Run nginx-proxy container and certificate helper container:

* Create new network
$ docker network create reverse-proxy-net

* Start your reverse proxy
$ docker-compose up -d

## Configuring your reverse-proxy
* Do NOT change docker-compose.yml, ONLY proxy.conf
### proxy.conf
* to add a new routing, add a new location mapping to the server segment
* the tag assigned to location is mapped to the **actual URL** 
* the **proxy_pass** must be the **docker service name** or **container name** of your application
* Example:
	* location /test{
        # Trailing slash is required in some cases, but not in others, simply test it 
        proxy_pass  http://plain-apache/;
    	} 
	* example service-name: plain-apache
	* example URL: example.com/test


## Run whole apps with docker-compose
* Run in background
$ docker-compose up -d
* For example setups with reverse-proxy, see the docker-compose.yml files in the respective sub-folders