FROM node:12
RUN mkdir -p /node
ADD . /node
WORKDIR /var/www/html
RUN npm install
RUN npm install -g laravel-echo-server
CMD ["node", "server.js"]