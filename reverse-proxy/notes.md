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
* the tag assigned to location must be the **docker service name** of your application
* the **proxy_pass** is mapped to the **actual URL**
* Example:
	* location /experiment-1 {
        # Add the trailing slash to the proxy_pass to reroute request to / 
        proxy_pass  http://php-experiment-1/;
    	} 
	* example service-name: experiment-1
	* example URL: example.com/php-experiment-1


## Run whole apps with docker-compose
* Run in background
$ docker-compose up -d
* For example setups with reverse-proxy, see the docker-compose.yml files in the respective sub-folders