WORKDIR /application
COPY . /application
RUN composer install
EXPOSE 9010
