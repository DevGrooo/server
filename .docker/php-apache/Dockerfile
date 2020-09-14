FROM php:7.3-apache

RUN apt-get update

# install php intl
RUN apt-get install -y zlib1g-dev libicu-dev g++
RUN docker-php-ext-configure intl
RUN docker-php-ext-install intl

# For php < 7.3
#RUN apt-get install -y \
#        libzip-dev \
#        zip \
#  && docker-php-ext-configure zip --with-libzip \
#  && docker-php-ext-install zip

# For php >= 7.3
RUN apt-get install -y \
        libzip-dev \
        zip \
  && docker-php-ext-install zip

# install php gd
# RUN apt-get install libpng-dev
# RUN docker-php-ext-install gd

RUN apt-get install -y libfreetype6-dev libjpeg62-turbo-dev && \
    docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/  &&  \
    docker-php-ext-install gd

# install php mysql
#RUN docker-php-ext-install mysqli
RUN docker-php-ext-install pdo pdo_mysql

# enable mod rewrite
RUN a2enmod rewrite

# install php composer

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php -r "if (hash_file('sha384', 'composer-setup.php') === 'e5325b19b381bfd88ce90a5ddb7823406b2a38cff6bb704b0acc289a09c8128d4a8ce2bbafcd1fcbdc38666422fe2806') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" && \
    php composer-setup.php && \
    php -r "unlink('composer-setup.php');" && \
    mv composer.phar /usr/local/bin/composer


