#!/bin/bash

# Creating error.log
mkdir -p ${LOG_DIR}
touch ${LOG_DIR}/error.log
chown ${USER_APACHE}:${USER_APACHE} ${LOG_DIR}/error.log

# Creating config
mkdir -p ${RABBITMQ_CONFIG_DIR}
touch ${RABBITMQ_CONFIG_FILE}
chown ${USER_APACHE}:${USER_APACHE} ${RABBITMQ_CONFIG_FILE}

# Update main httpd.conf
sed -i \
    -e 's~AllowOverride \(.*\)$~AllowOverride All~g' \
    -e 's~^ErrorLog \(.*\)$~ErrorLog "'${LOG_DIR}'/error.log"~g' \
    /etc/httpd/conf/httpd.conf

# Set php timezone
sed -i \
    -e 's~;date.timezone =~date.timezone = Europe/Moscow~g' \
    /etc/php.ini

# Update .htaccess
cat ${PRROOT}/docker/prepare.htaccess \
    | sed -e 's~<%=log_path %>~'${LOG_DIR}'~' \
    > ${PRROOT}/web/.htaccess

# Update rabbitmq config
cat ${PRROOT}/docker/prepare.rabbitmq \
    | sed -e 's~<%=host %>~'${RABBITMQ_HOST}'~' \
    | sed -e 's~<%=port %>~'${RABBITMQ_PORT}'~' \
    | sed -e 's~<%=user %>~'${RABBITMQ_USER}'~' \
    | sed -e 's~<%=pass %>~'${RABBITMQ_PASS}'~' \
    > ${RABBITMQ_CONFIG_FILE}

# Make sure we're not confused by old, incompletely-shutdown httpd
# context after restarting the container.  httpd won't start correctly
# if it thinks it is already running.
rm -rf /run/httpd/* /tmp/httpd*

exec /usr/sbin/apachectl -DFOREGROUND
