FROM ubuntu:16.04

# Set up locales
ENV LANG C.UTF-8
ENV LANGUAGE C.UTF-8
ENV LC_ALL C.UTF-8

# Initial apt update
RUN apt-get update && apt-get -y install wget curl git software-properties-common python-software-properties

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
    python-pip

# Install PHP7.4
RUN  apt-get -y install php7.4 php7.4-cgi php7.4-cli php7.4-common php7.4-curl php7.4-dev php7.4-gd php7.4-gmp php7.4-json php7.4-ldap php7.4-mysql \
  php7.4-odbc php7.4-opcache php7.4-pgsql php7.4-pspell php7.4-readline php7.4-sqlite3 php7.4-tidy php7.4-xml php7.4-xmlrpc php7.4-xdebug \
  libphp7.4-embed php7.4-bcmath php7.4-bz2 php7.4-enchant php7.4-fpm php7.4-imap php7.4-interbase php7.4-intl php7.4-mbstring php7.4-redis \
  php7.4-phpdbg php7.4-soap php7.4-sybase php7.4-xsl php7.4-zip php7.4-dba \
    php-memcached php-mysqlnd-ms php-mongodb php-imagick

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ && mv /usr/bin/composer.phar /usr/bin/composer

# Set the timezone Ho_Chi_Minh
RUN cp /usr/share/zoneinfo/Asia/Ho_Chi_Minh /etc/localtime

# Install Supervisor
RUN apt-get -y install supervisor
RUN mkdir -p /var/log/cache

RUN  apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* 

RUN update-alternatives --set php /usr/bin/php7.4

ADD ./conf.d/www.conf /etc/php/7.4/fpm/pool.d/www.conf
ADD ./conf.d/php.ini /etc/php/7.4/fpm/php.ini
ADD ./conf.d/php-fpm.conf /etc/php/7.4/fpm/php-fpm.conf
ADD ./supervisor/supervisord.conf /etc/supervisor/supervisord.conf

WORKDIR /var/www/html

EXPOSE 9074

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]