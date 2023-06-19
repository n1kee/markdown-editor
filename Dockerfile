FROM debian:bullseye-slim

WORKDIR /app

RUN apt-get update && apt-get install -y php7.4;

EXPOSE 80

CMD bash -c "/usr/bin/php7.4 -S 0.0.0.0:80 -t /app"