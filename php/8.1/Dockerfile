FROM ubuntu:20.04

# Set up locales
ENV LANG C.UTF-8
ENV LANGUAGE C.UTF-8
ENV LC_ALL C.UTF-8

# Initial apt update
RUN ln -snf /usr/share/zoneinfo/$CONTAINER_TIMEZONE /etc/localtime && echo $CONTAINER_TIMEZONE > /etc/timezone
RUN apt-get update && apt-get -y install wget curl git software-properties-common

# Install common / shared packages
RUN add-apt-repository ppa:ondrej/php
RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    vim \
    locales \
    ca-certificates \
    dialog \
    gcc \
    musl-dev \
    libffi-dev \
    iputils-ping \
    net-tools telnet \
    python \
    python-dev \
    python3-pip

# Install PHP8.1
RUN  apt-get -y install php8.1 php8.1-cgi php8.1-cli php8.1-common php8.1-curl php8.1-dev php8.1-gd php8.1-gmp php8.1-ldap php8.1-mysql \
  php8.1-odbc php8.1-opcache php8.1-pgsql php8.1-pspell php8.1-readline php8.1-sqlite3 php8.1-tidy php8.1-xml php8.1-xmlrpc php8.1-xdebug \
  libphp8.1-embed php8.1-bcmath php8.1-bz2 php8.1-enchant php8.1-fpm php8.1-imap php8.1-interbase php8.1-intl php8.1-mbstring php8.1-redis \
  php8.1-phpdbg php8.1-soap php8.1-sybase php8.1-xsl php8.1-zip php8.1-dba \
    php-memcached php-mysqlnd-ms php-mongodb php-imagick

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ && mv /usr/bin/composer.phar /usr/bin/composer

# Set the timezone Ho_Chi_Minh
RUN cp /usr/share/zoneinfo/Asia/Ho_Chi_Minh /etc/localtime

# Install Supervisor
RUN apt-get -y install supervisor
RUN mkdir -p /var/log/cache

RUN  apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* 

RUN update-alternatives --set php /usr/bin/php8.1

ADD ./conf.d/www.conf /etc/php/8.1/fpm/pool.d/www.conf
ADD ./conf.d/php.ini /etc/php/8.1/fpm/php.ini
ADD ./conf.d/php-fpm.conf /etc/php/8.1/fpm/php-fpm.conf
ADD ./supervisor/supervisord.conf /etc/supervisor/supervisord.conf

WORKDIR /var/www/html

EXPOSE 9074

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]