FROM ubuntu:20.04

# Set up locales
ENV LANG C.UTF-8
ENV LANGUAGE C.UTF-8
ENV LC_ALL C.UTF-8

# Initial apt update
RUN ln -snf /usr/share/zoneinfo/$CONTAINER_TIMEZONE /etc/localtime && echo $CONTAINER_TIMEZONE > /etc/timezone
RUN apt-get update && apt-get -y install wget curl git software-properties-common

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
    net-tools \
	python \
    python-dev \
    python3-pip

# Set time asia/ho_chi_minh
RUN \
    apt-get -y install tzdata && \
    ln -sf /usr/share/zoneinfo/Asia/Ho_Chi_Minh /etc/localtime && date

# Install Nginx.
RUN \
  add-apt-repository -y ppa:nginx/stable && \
  apt-get update && \
  apt-get install -y nginx nginx-extras && \
  rm -rf /var/lib/apt/lists/* && \
  echo "\ndaemon off;" >> /etc/nginx/nginx.conf && \
  chown -R www-data:www-data /var/lib/nginx

RUN	apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* 

# Define working directory.
WORKDIR /etc/nginx

# Expose ports.
EXPOSE 80 443

# Define default command.
CMD ["nginx"]