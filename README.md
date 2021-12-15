# hiwi-server-setup
## Docker and Docker-compose
* What does Docker and compose do ?
	* hosting all applications in separate containers with their own dependencies and environments
* How to install Docker and docker-compose 
	* sudo apt install docker docker-compose

## NGINX reverse-proxy
* What does a reverse-proxy do?
	* nginx reverse-proxy is used to host multiple apps under various subdomains, eg. mydomain.de/myapp-1,myapp_mydomain.de/myapp-2, ...
* To run and configure the nginx-reverse-proxy, check configuration.md in folder reverse-proxy

## Your experiments
### Running your experiment on the server
0. create a docker-compose.yml in your repository (if not yet exists), similar to the example ones in this repository
1. ssh onto the server
2. clone your git repository
3. cd into your experiment directory
4. run docker-compose up -d
5. DONE - now your experiment is running as configured in your docker-compose.yml

### Updating your running experiment
1. run docker stop YOUR_CONTAINER_NAME
2. pull your github repository
3. run docker-compose up -d
4. DONE - now your UPDATED experiment is running as configured in your docker-compose.yml