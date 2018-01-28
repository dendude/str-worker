FROM centos:7

RUN yum install -y epel-release
RUN rpm -Uvh https://mirror.webtatic.com/yum/el7/webtatic-release.rpm

RUN yum install -y yum-plugin-replace

RUN yum install -y httpd

RUN yum install -y mod_php71w php71w-common php71w-cli
RUN yum install -y php71w-bcmath php71w-mbstring
RUN yum install -y php71w-mysql php71w-pdo

RUN yum install -y rabbitmq-server

ENV PR_ROOT=/var/www/project
ENV LOG_DIR=${PR_ROOT}/logs
ENV USER_APACHE=apache

RUN rm -rf /var/www/html && ln -nfs ${PR_ROOT}/web /var/www/html
RUN rm /etc/httpd/conf.d/welcome.conf

RUN rabbitmq-server start
RUN php ${PR_ROOT}/web/_service.php &

EXPOSE 80

ADD run-httpd.sh /run-httpd.sh
CMD ["/run-httpd.sh"]
