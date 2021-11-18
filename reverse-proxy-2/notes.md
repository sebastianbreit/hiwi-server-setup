docker run --rm --name whoami -e VIRTUAL_HOST=memo.hib.uni-tuebingen.de -e VIRTUAL_PATH=/whoami -e VIRTUAL_PORT=80 --network reverse-proxy-net -d jwilder/whoami

docker run --rm --name whoami -e VIRTUAL_HOST=memo.hib.uni-tuebingen.de -e VIRTUAL_PATH=/whoami -e VIRTUAL_PORT=80  -d jwilder/whoami --expose=8000


                                                                                         
version: '2'

services:
  reverse-proxy:
    image: "nginxproxy/nginx-proxy"
    container_name: "reverse-proxy"
    volumes:
        - "html:/usr/share/nginx/html"
        - "dhparam:/etc/nginx/dhparam"
        - "vhost:/etc/nginx/vhost.d"
        - "certs:/etc/nginx/certs"
        - "/run/docker.sock:/tmp/docker.sock:ro"
    restart: "always"
    networks:
        - "reverse-proxy-net"
    ports:
        - "80:80"
        - "443:443"

  whoami:
    image: jwilder/whoami
    expose:
      - "8000"
    environment:
      - VIRTUAL_HOST=memo.hib.uni-tuebingen.de
      - VIRTUAL_PORT=8000
      - VIRTUAL_PATH=/whoami
    networks:
      - "reverse-proxy-net"

volumes:
  certs:
  html:
  vhost:
  dhparam:
networks:
  reverse-proxy-net:
    external: true
